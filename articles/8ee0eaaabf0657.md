---
title: "先日編み出した帳票印刷のベストプラクティスをnpmライブラリとして公開しました"
emoji: "🐘"
type: "tech"
topics: ["帳票", "adobexd", "figma", "javascript", "npm", "php"]
published: true
published_at: 2021-06-14
---

:::message
この記事は、2021-06-14に別のブログ媒体に投稿した記事のアーカイブです。
:::

# はじめに

先日公開した [ついに、Webアプリでの帳票印刷のベストプラクティスを編み出しました](https://zenn.dev/ttskch/articles/1f1572cfd2e375) という記事が、はてなブックマークで [1000以上もブックマークされ](https://b.hatena.ne.jp/entry/s/blog.ttskch.comhttps://zenn.dev/ttskch/articles/1f1572cfd2e375/) 大変多くの方に読んでいただきました。

それだけ帳票印刷という分野がいまだに多くの開発者にとって扱いづらく苦労の絶えない領域なのだということだと思います。皆さんいつもお疲れさまです🙏🍵

# ライブラリ化しました

今回、上記の記事でご紹介した方法をもっと手軽に実践できるように、npmライブラリ化して公開しました🙌

[![ttskch/svg-paper](https://opengraph.githubassets.com/599a460321f975706e20a7da1ae4c5876b2af15c3edf8ea78be81d064a9a48dc/ttskch/svg-paper)](https://github.com/ttskch/svg-paper)

以下に使い方などを簡単にご紹介します。

# インストール

CDN（[jsDelivr](https://www.jsdelivr.com/package/npm/svg-paper)）経由で簡単に利用できます。

```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/svg-paper/dist/svg-paper.min.css">
```

```html
<script src="https://cdn.jsdelivr.net/npm/svg-paper/dist/svg-paper.min.js"></script>
```

もちろんnpmでもインストール可能です。

```bash
$ npm install svg-paper
```

> ちなみに、`<script>` タグでの読み込みとモジュールとしてのインポートの両方に対応したライブラリの作成方法は以下の記事を参考にしました🙏
>
> [ブラウザとnode.jsに両対応したライブラリを作りたいときのWebpackレシピ集 - Qiita](https://qiita.com/riversun/items/1da0c0668d0dccdc0460)

# テンプレートSVGの準備

このライブラリを使用する前にそもそもテンプレートとするSVGを [Adobe XD](https://www.adobe.com/jp/products/xd.html) や [Figma](https://www.figma.com/) などを使って作成する必要があります。

この辺りの具体的な方法については（[先日のブログ記事](https://zenn.dev/ttskch/articles/1f1572cfd2e375) でも簡単に説明しましたが）以下のドキュメントに詳細をまとめてありますので、ご参照ください🙏

[How to prepare SVG template](https://github.com/ttskch/svg-paper/blob/224881ddc46db1e8a1caebd110cf7f6614e0c6d9/docs/how-to-prepare-svg-template.md)

Adobe XDで作成した場合はほとんど何も考えなくていいのですが、Figmaで作成した場合は

* `id` 属性に使用不可能な文字があっても自動で置換などされないので自分で `sed` コマンドなどで適切に修正する必要がある
* プレースホルダーに日本語文字を使うと [XMLの文字参照](https://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references#Character_reference_overview) に置き換えられてしまい扱いづらいので、英語で書くようにするのが無難

という2点に注意が必要です。

# svg-paper自体の基本的な使い方

まず、準備しておいたSVGテンプレートをHTMLの `<body>` 直下などに埋め込みます。

```html
<body>
  <svg>...</svg>
</body>
```

そして、`svg-paper.js` （または `svg-paper.min.js` ）を `<script>` タグで読み込むか、JSファイルにモジュールとしてインポートします。

```html
<script src="svg-paper.min.js"></script>
<script>
  const paper = new SvgPaper()
  // ...
</script>
```

```js
import SvgPaper from 'svg-paper'
// or
// const SvgPaper = require('svg-paper')

const paper = new SvgPaper()
```

これで、svg-paperを使ってDOM内のSVGコンテンツを置換したりサイズ調整したりといった操作を以下のように簡単に行うことができるようになります。

```js
paper
  // プレースホルダーを実際の値に置換する
  .replace('%placeholder1%', '実際の値')
  // ... 他にもあれば同様に

  // テキストの最大幅を1000に設定
  // 実際のコンテンツ幅が1000以下の場合は何も起こらず、1000を超えたときだけ自動で縮小する
  .adjustText('#selector1', 1000)

  // テキストの最大幅を800に設定し、#selector2 要素を幅800の中央に寄せる
  .adjustText('#selector2', 800, 'middle')

  // 同様に、右寄せする
  .adjustText('#selector3', 800, 'end')

  // #selector4 要素を 600 x 300 のエリアに収まるように自動で折り返し＆縮小する
  .adjustTetxarea('#selector4', 600, 300)

  // エキストエリアの調整には他にも以下のようなオプションが指定可能
  .adjustTextarea('#selector5',
    600,  // テキストエリアの幅
    300,  // テキストエリアの高さ
    1.2,  // 行の高さ        : デフォルトはフォントサイズの1.2倍
    0.5,  // x方向の余白     : デフォルトはフォントサイズの0.5倍
    0.5,  // y方向の余白     : デフォルトはフォントサイズの0.5倍
    false // 自動折り返しフラグ : デフォルトはfalse。trueにすると自動折り返しされなくなる
  )

  // 最後に、置換やテキスト/テキストエリアの調整を実際にDOMに適用する
  .apply()
```

プレビュー画面をそれっぽい見た目にするには、以下のようにHTMLに3行だけ追記してスタイルを当ててあげればOKです。

```html
<head>
  ...
  <link rel="stylesheet" href="svg-paper.min.css"> <!-- これ -->
  <style>@page { size: A4 }</style> <!-- これ -->
</head>

<body class="A4"> <!-- これ -->
  <svg>...</svg>
</body>
```

指定できるサイズは `A4` （A4縦）の他にも

* `A3` `A3 landscape`
* `A4` `A4 landscape`
* `A5` `A5 landscape`
* `letter` `letter landscape`
* `legal` `legal landscape`

があります。（ `landscape` は横向き）

# バックエンドからフロントエンドへの値の受け渡し

svg-paperはテキストの自動縮小に対応するためにどうしてもDOMに依存する必要があったため、クライアントサイドでしか使えません。

なので、一般的なユースケースにおいては、プレースホルダーを実際の値に置換するにあたってバックエンドからフロントエンドへ値を受け渡す必要があります。

もっとも簡単な方法は、JSON文字列にしてビューに埋め込んでしまうことでしょう。

以下はPHP（ビューはTwig）での実装例です。他の言語やテンプレートエンジンでも本質はまったく同じなので適宜読み替えてください🙏

```php
// Controller
public function paperAction($id)
{
    $model = $repository->findById($id);
    
    return $this->render('paper.html.twig', [
        'svg' => file_get_contents('/path/to/paper.svg'),
        'replacements' => [
            '%name%' => $model->name,
            // ... 他にもあれば同様に
        ],
    ]);
}
```

```twig
{# paper.html.twig #}
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="svg-paper.min.css">
  <style>@page { size: A4 }</style>
</head>

<body class="A4">
  {{ svg|raw }}
  <div data-replacements="{{ replacements|json_encode }}"></div>

  <script src="svg-paper.min.js"></script>
  <script src="your-script.js"></script>
</body>
</html>
```

```js
// your-script.js
const paper = new SvgPaper()

const replacements = $('[data-replacements]').data('replacements')

for (let [search, replacement] of Object.entries(replacements)) {
  paper.replace(search, replacement)
}

paper.apply()
```

# Tips: プレースホルダーを置換する前のコンテンツが一瞬表示されちゃう問題の対処法

svg-paperはDOMがロードされた後で置換や調整を行うため、そのままだと置換する前の状態のコンテンツが一瞬表示されてしまいます。

この問題は、以下のように目隠し用のレイヤーを1枚用意しておいて `.apply()` メソッド実行後にそのレイヤーを非表示にするといった対応で簡単に解決できます👍

```html
<body>
  <div id="blinder" style="width:100vw; height:100vh; background-color:#ccc"></div>
  <svg>...</svg>
</body>
```

```js
paper.apply()

document.querySelector('#blinder').style.display = 'none'
```

# まとめ

というわけで、僕の考えた最強の帳票印刷をnpmライブラリ化した `svg-paper` をご紹介しました。ぜひ使ってみていただけると嬉しいです！

[![ttskch/svg-paper](https://opengraph.githubassets.com/599a460321f975706e20a7da1ae4c5876b2af15c3edf8ea78be81d064a9a48dc/ttskch/svg-paper)](https://github.com/ttskch/svg-paper)
