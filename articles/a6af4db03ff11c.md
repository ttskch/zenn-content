---
title: "Angular8アプリをNetlifyで無料でホスティングしてみよう"
emoji: "💻"
type: "tech"
topics: ["javascript", "typescript", "angular", "netlify"]
published: true
published_at: 2020-02-12
---

:::message
この記事は、2020-02-12に別のブログ媒体に投稿した記事のアーカイブです。
:::

こんにちは、たつきちです。

エンジニア歴12年ぐらいで今はベンチャー企業のCTOをしています。

この記事では、Angularのアプリを最近流行りのNetlifyを使って無料でホスティングする方法について解説していきます。

> Angularのバージョンは8を例に説明します。

ぜひ最後までお付き合いください。

# Netlifyとは

[Netlify](https://www.netlify.com/) （「ネットリファイ」と読むようです）は、静的サイトを無料からホスティングできるWebサービスです。

GitHubリポジトリと連携しておけば、リポジトリが更新されたタイミングで自動でビルド＆デプロイしてくれるので、GitHub Pagesをもっと便利にした感じのサービスと言うと分かりやすいかもしれませんね。

# Netlifyにサイトを登録する

キャプチャを並べながら実際の手順を説明していきます。

まずは、

<https://app.netlify.com/signup>

にアクセスしてアカウントを登録しましょう。GitHubアカウント等でサインアップ可能です。

![GitHubアカウント等でサインアップ](https://user-images.githubusercontent.com/4360663/74221530-ac526180-4cf5-11ea-8160-b18b92512252.png)

サインアップに成功すると、このような「サイト一覧画面」になります。

![Netlifyのサイト一覧画面](https://user-images.githubusercontent.com/4360663/74221851-6053ec80-4cf6-11ea-8d52-c01fdbd57b7f.png)

初めは1つもサイトがないので、右上の `New site from Git` からサイトを追加します。

> ちなみに、ユーザー（チーム）名はデフォルトだと `Takashi Kanemoto's team` のような名前になりますが、メニュー右端の `Team Settings` から `Edit team information` へ行くとチーム名を変更できます。

`New site from Git` をクリックすると以下のような画面になりますので、連携したいサービスを選択します。

![Netlifyのサイト作成画面](https://user-images.githubusercontent.com/4360663/74221981-af018680-4cf6-11ea-9195-874a9b413a48.png)

さらに、連携したいリポジトリを、リポジトリ名の一部で検索するなどして見つけ、選択します。

![連携するリポジトリを選択](https://user-images.githubusercontent.com/4360663/74222133-128bb400-4cf7-11ea-8764-19265b5c33a6.png)

選択すると以下のような画面になり、デプロイとビルドの設定ができます。

* どのブランチを自動デプロイするか
* ビルドコマンドは何か
* 公開ディレクトリのパスはどこか

![デプロイとビルドの設定](https://user-images.githubusercontent.com/4360663/74222261-70200080-4cf7-11ea-8a72-3f63e81c2d22.png)

Angularアプリの場合なら、

* ビルドコマンド： `ng build`
* 公開ディレクトリ： `dist/{app-name}`

とう感じになるかと思います。

入力したら、 `Deploy site` をクリックするとサイトの追加は完了です。（この辺りの設定は後から変更もできます👌）

作成されたサイトを選択すると、サイト概要画面が見られます。

![サイト概要画面](https://user-images.githubusercontent.com/4360663/74223270-acecf700-4cf9-11ea-9152-096d6a742dae.png)

デプロイの完了を待って、上図の赤枠の部分に表示されている公開用URLにアクセスすれば、サイトが閲覧できるかと思います🙌

# サイト名・URLを分かりやすいものに変更する

ところで、作成直後はサイト名が複雑な文字列になっているので、分かりやすい名前に変更しておきましょう。（サイト名がそのままURLのサブドメインになります）

`Site settings` をクリックします。（または、メニュー右端の `Settings` からでも同じ画面に行けます）

![サイト概要画面](https://user-images.githubusercontent.com/4360663/74222976-0274d400-4cf9-11ea-9bad-ed7b13d88601.png)

`Site information` ブロックの `Change site name` をクリックします。

![Change site nameを選択](https://user-images.githubusercontent.com/4360663/74222744-84b0c880-4cf8-11ea-97bd-010e213c9ca3.png)

好きな名前に変更しましょう。

![サイト名変更画面](https://user-images.githubusercontent.com/4360663/74222861-ba55b180-4cf8-11ea-9d92-b1571f4297af.png)

# index.htmlへのリダイレクトを設定する

AngularアプリのようなSPAの場合、ルートパス（ `/index.html` ）以外へのアクセスを **すべてルートパスにリダイレクトしてあげる必要があります。**

今のままだと、

* `/` は開ける
* `/` から画面遷移した場合は他のページも開ける
* でも `/` 以外のURLに直接アクセスすると404になる

という動作になってしまいます。

というわけで、リダイレクトの設定をしてあげましょう。

Netlifyでは、ドキュメントルートに `_redirects` という特殊な設定ファイルを置いておくことによって [自由にリダイレクトを設定できる](https://docs.netlify.com/routing/redirects/#syntax-for-the-redirects-file) ようになっています。

今回のような場合なら、ドキュメントルートに

```
/*   /index.html   200
```

という内容で `_redirects` というテキストファイルを設置すればよいです。

Angularでこれを実現するには、 `_redirects` ファイルをアセットとしてドキュメントルートに配置してくれるように `angular.json` を設定する必要があります。

具体的には以下のようにすればOKです。

* `src/_redirects` を作成
* `angular.json` の `build.assets` に `"src/_redirects"` を追加

```diff
"assets": [
  "src/favicon.ico",
- "src/assets"
+ "src/assets",
+ "src/_redirects"
],
```

これをGitHubにpushしてデプロイしてもらえば対応完了です👍

# まとめ

* Netlifyは無料でSPAのホスティングができてめっちゃ便利！
* SPAの場合は `_redirects` ファイルの設定を忘れずに！

では、よきNetlifyライフを！
