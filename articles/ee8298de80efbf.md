---
title: "[2020年版] PhpStorm＋Xdebugでステップ実行する方法 [これで100%できる]"
emoji: "🐘"
type: "tech"
topics: ["php", "phpstorm"]
published: true
published_at: 2020-04-22
---

:::message
この記事は、2020-04-22に別のブログ媒体に投稿した記事のアーカイブです。
:::

PhpStormでWebサイトのソースコードをステップ実行する方法を解説します。

意外とググっても的確な情報が出てこなかったり、出てきても古過ぎて微妙に条件が違ってたりするなーと思ったので、改めて整理しておきます。

この記事のとおりにやれば誰でもすぐにステップ実行でデバッグができるようになると思いますので、参考にしてみてください👍

# この記事の動作環境

一応僕の環境を書いておきますが、多少環境が違っても基本的に同じ手順で大丈夫なはずです。

* PhpStorm `2020.1`
* PHP `7.3.14`
* Xdebug `2.9.2`
* Google Chrome `81.0.4044.113 (Official Build) (64ビット)`

> Xdebugのバージョンは [`phpversion('xdebug');`](https://www.php.net/manual/ja/function.phpversion.php) で確認

# 具体的なやり方

## 1. Xdebugのリモートデバッグ有効化する

PhpStormでのステップ実行は、Xdebugのリモートデバッグ機能を使って行われます。なので、あらかじめXdebugを適切に設定しておくことが必要です。

> リモートデバッグ機能については [こちら](https://qiita.com/castaneai/items/d5fdf577a348012ed8af) の記事が詳しいです。

と言っても、Xdebugに追加で設定する必要があるのは

```
xdebug.remote_enable=1
```

これだけです👌

```bash
$ php -i | grep xdebug
```

の出力を見て、ちゃんとXdebugが有効になっていて `xdebug.remote_enable` がOnになっていることを確認しておきましょう。

```
xdebug support => enabled
 :
xdebug.remote_enable => On => On
 :
```

## 2. PhpStormのXdebug連携の設定内容を確認する

次に、PhpStormでXdebugのリモートデバッグ機能が正しく使えるように、設定内容を確認します。

`Preference > Languages & Frameworks > PHP > Debug` を開いて、 `Xdebug` の箇所を確認してください。

下図のように、 `Debug port:` が `9000` になっていて、 `Can accept external connections` にチェックが入っていればOKです。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1qgeuz3jj313w0u07ls.jpg)

なお、 `9000` というのはXdebugの設定値 [xdebug.remote_port](https://xdebug.org/docs/all_settings#remote_port) のデフォルト値です。

もしXdebugの設定を変更している場合は、PhpStormのここの設定もそれに合わせて変える必要があります。

```bash
$ php -i | grep xdebug.remote_port
xdebug.remote_port => 9000 => 9000
```

でXdebugに設定されているポート番号を調べてください✋

## 3. ブラウザにXdebugを利用するための拡張を入れる

次に、ブラウザからXdebugを利用できるようにする必要があります。

> 詳しくは [こちらのヘルプページ](https://pleiades.io/help/phpstorm/browser-debugging-extensions.html) をご参照ください。

Chromeの場合なら、[こちらの拡張機能](https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc) をインストールすればOKです。

他のブラウザで利用したい場合は、上記ヘルプページのリンクから対応する拡張機能をインストールするか、[こちらのページ](https://www.jetbrains.com/phpstorm/marklets/) でXdebug用の `Start debugger` `Stop debugger` 2つのブックマークレットを取得して、ブックマークに登録しておけばOKです。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1r1nri0wj30u10u0qav.jpg)

## 4. 実際にステップ実行してみる

以上で準備は完了です。実際にステップ実行してみましょう。

適当なサイトのソースコードをPhpStormで開いたら、画面右上あたりにある受話器のようなアイコンをクリックして、デバッガーからの接続のリスニングを開始します。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1robrt1tj31f60u015z.jpg)

`Run > Start Listening for PHP Debug Connections` からでも実行できます。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1r9yd40mj30jm0p2ha4.jpg)

下図のように受話器がリスニング中っぽいアイコンに変わったら接続待ち状態です。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1rdfxfr2j301k01ia9w.jpg)

ステップ実行を行うために、適当な行にブレークポイントを張っておきましょう。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1rkkpm0cj30d007k3ys.jpg)

では次に、ブラウザから接続を開始します。

先ほどインストールしたブラウザの拡張機能で、デバッガーを開始させてください。Chrome拡張の場合は下図のとおり `Debug` ボタンをクリックします。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1rf3d98zj31f60u0al1.jpg)

デバッガーが開始すると、虫が緑色になります。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1rh0t5hmj301w01qa9y.jpg)

これで接続完了です。

サイトを読み込んでみましょう。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1rsqpqfxg313x0m4b29.gif)

リロードした瞬間にPhpStormにフォーカスが移って、

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1rtvqe9ej30u00uualf.jpg)

こんな感じの確認画面が表示されました。Acceptするとそのままブレークポイントで処理が止まって、そこからステップ実行ができる状態になりました。

Acceptしたことによって `Preference > Languages & Frameworks > PHP > Servers` に以下のような設定が自動で追加されています。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1rvl7iozj315i0u019j.jpg)

これが追加されたので、2回目以降は確認画面なしでいきなりデバッグが始まります。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1rxk3j0qg313x0m44qp.gif)

## 5. 使い終わったらブラウザのデバッガーとPhpStormのリスニング状態を止める

用が済んだら、忘れずにブラウザのデバッガーを止めてPhpStormのリスニング状態を解除しておきましょう。

Chrome拡張なら、 `Disable` をクリックして止めます。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1rzk7eeuj307w0bojrm.jpg)

PhpStorm側は、受話器のアイコンをこの状態に戻します。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1s1aeu64j301o01kjr8.jpg)

# まとめ

PhpStorm＋Xdebugでステップ実行する方法を丁寧に解説してみました。参考になれば幸いです。
