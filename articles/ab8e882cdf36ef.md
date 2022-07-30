---
title: "[Symfony] ログイン情報を変更する操作の機能テストをBasic認証で行っている場合の落とし穴"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "機能テスト"]
published: true
published_at: 2020-05-01
---

:::message
この記事は、2020-05-01に別のブログ媒体に投稿した記事のアーカイブです。
:::

Symfonyの機能テストでちょっとハマったのでメモしておきます。

https://twitter.com/ttskch/status/1253263948901527558

# どういうことか

↑のツイートでほぼすべて言い終わってますが一応補足説明します。

Symfonyでログイン後の画面を機能テストする際、ログインの処理を簡単にするためにBasic認証を使うのが定石です。（詳しくは [こちらの記事](https://blog.ttskch.com/symfony-simulate-login-on-functional-test/) をご参照ください）

この方法を使っているときに、ログイン情報（ユーザー名またはパスワード）を変更したあとの画面の動作を機能テストしたい場合、ちょっと注意が必要です。

例えば、以下のような用件を考えてみます。

* メールアドレスとパスワードでログインする
* メールアドレス認証（メールアドレスの所有者確認）の機能がある
* ユーザー編集画面でメールアドレスを変更したら「メールアドレス認証未完了」状態に変わって、画面に「認証してください」的な表示が出る

このとき、ユーザー編集画面の機能テストを以下のような感じで書いたとしましょう。

```php
class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected function setUp(): void
    {
        $this->loadFixtureFiles([
            __DIR__.'/../fixtures/Controller/UserControllerTest.yaml',
        ]);
    }

    public function testEditAction()
    {
        // user@test.com ユーザーでログインして /user/edit にアクセス
        $client = $this->createAuthorizedClient('user@test.com', 'password');
        $crawler = $client->request('GET', '/user/edit');

        // メールアドレスを変更
        $form = $crawler->selectButton('保存')->form();
        $form->setValues([
            'user[email]' => 'user+changed@test.com',
        ]);
        $client->submit($form);

        // ユーザートップページに飛ばされて、画面に「メールアドレス認証をしてください」が表示される
        $this->assertResponseRedirects('/user/');
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-warning', 'メールアドレス認証をしてください');
    }

    private function createAuthorizedClient(string $username, string $password): KernelBrowser
    {
        self::ensureKernelShutdown();

        return static::createClient([], [
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW' => $password,
        ]);
    }
}
```

一見問題なさそうに見えませんか？

実はこのテストは意図どおりに動作しません。

なぜなら、ログインIDであるメールアドレスが変更されたにもかかわらず、その後のリクエストもすべて古いメールアドレスを使ってBasic認証しようとしているからです。

普通に画面から操作しているときはログイン情報を変更してもブラウザからのリクエストは同じセッションクッキーを送るだけでいいので特に意識しませんが、Basic認証の場合は毎回のリクエストにログイン情報を付加して送るので、ログイン情報を変更したあとはリクエストに付加するログイン情報もあわせて変更する必要があります。

> 普通に画面から操作しているときとBasic認証のときの違いがイメージできない方は、[こちらの記事](https://qiita.com/toshiya/items/e7dcc7610b15884b167e) などが参考になるかもしれません。

# どうすればいいか

では、その点を踏まえて先ほどのテストコードを期待どおりに動作するように書き換えてみましょう。

```diff
class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected function setUp(): void
    {
        $this->loadFixtureFiles([
            __DIR__.'/../fixtures/Controller/UserControllerTest.yaml',
        ]);
    }

    public function testEditAction()
    {
        // user@test.com ユーザーでログインして /user/edit にアクセス
        $client = $this->createAuthorizedClient('user@test.com', 'password');
        $crawler = $client->request('GET', '/user/edit');

        // メールアドレスを変更
        $form = $crawler->selectButton('保存')->form();
        $form->setValues([
            'user[email]' => 'user+changed@test.com',
        ]);
        $client->submit($form);

-       // ユーザートップページに飛ばされて、画面に「メールアドレス認証をしてください」が表示される
-       $this->assertResponseRedirects('/user/');
-       $crawler = $client->followRedirect();
-       $this->assertSelectorTextContains('.alert.alert-warning', 'メールアドレス認証をしてください');

+       // ユーザートップページに飛ばされようとすることを確認
+       $this->assertResponseRedirects('/user/');
+
+       // 変更後のメールアドレスでユーザートップページにアクセスし、画面に「メールアドレス認証をしてください」が表示されていることを確認
+       $client->setServerParameter('PHP_AUTH_USER', 'user+changed@test.com');
+       $crawler = $client->request('GET', '/user/');
+       $this->assertSelectorTextContains('.alert.alert-warning', 'メールアドレス認証をしてください');
    }

    private function createAuthorizedClient(string $username, string $password): KernelBrowser
    {
        self::ensureKernelShutdown();

        return static::createClient([], [
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW' => $password,
        ]);
    }
}
```

こんな感じにすれば意図したとおりにテストできます。

```php
$client->setServerParameter('PHP_AUTH_USER', 'user+changed@test.com');
```

でログインに使うメールアドレスを変更しているわけですね。

また、画面の動きとしては「ユーザー情報変更後はユーザートップページに飛ばされる」という実装なのですが、Basic認証の場合はそのままリダイレクトに従ってもヘッダーに付加するログイン情報が古くなってしまっているのでログインできません。（一般的な実装であれば、ログイン画面にリダイレクトされるでしょう）

なので、 `followRedirect()` は使えないので、意図どおりのリダイレクトレスポンスが返ってきていることだけ確認して、改めてユーザートップページにアクセスすることで、通常の画面の流れを擬似的にテストしているわけです。

# まとめ

* ログイン情報を変更する操作の機能テストをBasic認証で行っている場合、変更後はHTTPクライアントに新しいログイン情報をセットしなおしてからリクエストする必要があるので要注意
