---
title: "ページ内リンクのスクロール位置調整のために見出しの上に見えないマージンを付けている場合にhoverの当たり判定が大きくなる問題の解決策"
emoji: "💻"
type: "tech"
topics: ["css"]
published: true
published_at: 2020-05-12
---

:::message
この記事は、2020-05-12に別のブログ媒体に投稿した記事のアーカイブです。
:::

# これは何

タイトルでピンと来る人は少ないかもしれませんが、意外と頻繁に直面する問題です。

以前 [こちらの過去記事](https://zenn.dev/ttskch/articles/81c7e670b2d6dc) にまとめましたが、

* `position: fixed` なグローバルナビがある
* `<h2 id="見出し">見出し</h2>` のように見出しタグに `id` を付与している

のような場合に、 `#見出し` を目掛けてページ内リンクすると、 **`<h2 id="見出し">見出し</h2>` がグローバルナビの下に潜り込む** ような位置まで画面がスクロールしてしまうという、よく知られた問題があります。 

[`scroll-padding-top` プロパティ](https://developer.mozilla.org/ja/docs/Web/CSS/scroll-padding-top) を採用できる場合は

```css
html {
  scroll-padding-top: 70px;
}
```

これだけで解決なのですが、採用できない場合の一般的な解決策は、

```css
h1[id]:before, h2[id]:before, h3[id]:before, h4[id]:before, h5[id]:before, h6[id]:before {
  content: '';
  display: block;
  height: 70px;
  margin-top: -70px;
}
```

といった具合に、 `:before` 擬似要素を作ってある程度の高さを与え、高さと同じだけのネガティブマージンをセットするという方法です。

こうしておけば、ページ内リンクしたときのスクロール位置は、 **見出しタグの上の見えないマージンの先頭** になってくれるというわけですね。

# ここで問題が

さて、ここでちょっと追加の要件として、下図のように見出しタグをマウスオーバーすると `<a href="#見出し">#</a>` のようなリンクが出てくるというUIを実現することを考えてみましょう。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1gepd3qcoktg304h02d0ss.gif)

マークアップは

```html
<h2 id="見出し">
  <a href="#見出し">#</a>
  見出し
</h2>
```

こんな感じです。

これに先のCSSを適用してみましょう。

```css
h1[id]:before, h2[id]:before, h3[id]:before, h4[id]:before, h5[id]:before, h6[id]:before {
  content: '';
  display: block;
  height: 70px;
  margin-top: -70px;
}
```

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1gepd18mpu9g3064069q36.gif)

お分かりいただけたでしょうか…

**見出しタグの上の見えないマージンが直前の要素に被さっていて、リンクがクリックできない状態になってしまっています。**

また、見出しタグにマウスオーバーしたときに `#` が出現する仕様ではありますが、 **そのマウスオーバーの当たり判定には「見えないマージン」の部分も含まれている** ため、実際には見出しからかなり離れた場所をマウスオーバーした時点ですでに `#` が出現してしまっています。

これは美しくないし、特に前者は普通にユーザービリティに支障を来たしていますね。

前置きが長くなってしまいましたが、この記事ではこの問題の解決方法を解説します！

# 結論

先に結論を書きます。

何がどうなっているのかは後半で解説するので、理解したい人は読んでみてください✋

## 1. 見出しタグのマークアップをいじれる場合

以下のように見出しタグのマークアップを修正した上で、

```html
<h1 id="見出し">
  <span>
    <a href="#見出し">#</a>
    見出し
  </span>
</h1>
```

以下のようなCSSを適用すればいい感じになると思います。

```css
h1, h2, h3, h4, h5, h6 {
  position: relative;
  pointer-events: none;
}

h1:before, h2:before, h3:before, h4:before, h5:before, h6:before {
  content: '';
  display: block;
  height: 70px;
  margin-top: -70px;
}

h1 span, h2 span, h3 span, h4 span, h5 span, h6 span {
  pointer-events: auto;
}

h1 span a, h2 span a, h3 span a, h4 span a, h5 span a, h6 span a {
  opacity: 0;
}

h1:hover span a, h2:hover span a, h3:hover span a, h4:hover span a, h5:hover span a, h6:hover span a {
  opacity: 1;
}
```

## 2. 見出しタグのマークアップをいじれない場合

見出しタグのマークアップが以下のような形だとして、

```html
<h1 id="見出し">
  <a href="#見出し">#</a>
  見出し
</h1>
```

以下のようなCSSを適用すればいい感じになると思います。

```css
h1, h2, h3, h4, h5, h6 {
  position: relative;
  pointer-events: none;
}

h1:before, h2:before, h3:before, h4:before, h5:before, h6:before {
  content: '';
  display: block;
  height: 70px;
  margin-top: -70px;
}

h1:after, h2:after, h3:after, h4:after, h5:after, h6:after {
  content: '';
  display: block;
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  height: calc(100% - 70px);
  pointer-events: auto;
}

h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
  opacity: 0;
  pointer-events: auto;
}

h1:hover a, h2:hover a, h3:hover a, h4:hover a, h5:hover a, h6:hover a {
  opacity: 1;
}
```

# 解説

## 1. 見出しタグのマークアップをいじれる場合

[Bootstrapのドキュメントサイト](https://getbootstrap.com/docs/4.4/getting-started/introduction/) がこの問題を鮮やかに解決していました。

こちらでは、見出しタグ周りが以下のような感じで実装されています。

```html
<h2 id="見出し">
  <span>
    見出し
    <a href="#見出し">#</a>
  </span>
</h2>
```

```css
h2 {
  position: relative;
  pointer-events: none;
}

h2:before {
  content: '';
  display: block;
  height: 6rem;
  margin-top: -6rem;
}

h2 span {
  pointer-events: auto;
}

h2 span a {
  opacity: 0;
}

h2:hover span a {
  opacity: 1;
}
```

やっていることは以下のとおりです。

* 一番外側の `h2` 自身に [`pointer-events: none`](https://developer.mozilla.org/ja/docs/Web/CSS/pointer-events) をセットしてポインターイベントの対象外にする
* `:before` 擬似要素で「見えないマージン」を追加（これは先に説明したもの）
* 見出しのテキストとリンク要素は `h2 span` の中に入れて、 `h2 span` に対して `pointer-events: auto` をセット
    * これにより、 `h2 span` に対してのみポインターイベントが有効になり、なおかつ `h2 span` に対するポインターイベントは親である `h2` のイベントリスナーをトリガーするようになる（[参考](https://developer.mozilla.org/ja/docs/Web/CSS/pointer-events#Values)）
    * つまり、 `h2 span` にだけマウスオーバーの当たり判定がついて、なおかつ `h2 span` にマウスオーバーすることで親である `h2` が `:hover` 状態になる

鮮やかな解決方法ですね〜。

見出しタグのテキストを `span` で囲うことで、テキスト部分だけにピンポイントで `pointer-eventes: auto` をセットしているところがポイントです。

見出しタグのマークアップを自分でいじれる場合は、この方法で解決するのがよいと思います👍

👉というわけで、最終的なCSSの例は [こちら](#_1-見出しタグのマークアップをいじれる場合) のようになります。

## 2. 見出しタグのマークアップをいじれない場合

しかし、場合によっては自分でマークアップをいじれないこともあると思います。

例えば、[VuePress](https://vuepress.vuejs.org/) のような静的サイトジェネレータを使っていて、Markdownパーサーによってコンパイルされたhtmlをそのまま出力せざるを得ない場合などです。

仮に、マークアップが以下の形から変更できないとしましょう。

```html
<h2 id="見出し">
  <a href="#見出し">#</a>
  見出し
</h2>
```

このとき、なんとなく「見えないマージンの部分のポインターイベントを無効にすれば良さそう！」と思って以下のようなコードを書きがちですが、実はこれでは意味がありません。

```diff
h2:before {
  content: '';
  display: block;
  height: 70px;
  margin-top: -70px;
+ pointer-events: none;
}
```

なぜなら、 `h2:before` よりも上位の `h2` の `pointer-events` がデフォルトの `auto` になっているままなので、子である `h2:before` でいくら無効化しても無意味なのです。

やるべきことは、Bootstrapの例で見たように、

* 一番外側の `h2` 自身を `pointer-events: none` にする
* 内側にある見出しのテキスト部分（およびリンク要素）だけを `pointer-events: auto` にする

です。

しかし今回は見出しのテキスト部分が `span` で囲われたりしていないので、テキスト部分だけを `pointer-events: auto` にする術がありません。

どうするか。

答えは、 **`:after` 擬似要素をテキスト部分と同じ大きさで作って、それを `pointer-events: auto` にする** です。

具体的なコードは以下のようになります。

```css
h2 {
  position: relative;
  pointer-events: none;
}

h2:before {
  content: '';
  display: block;
  height: 70px;
  margin-top: -70px;
}

h2:after {
  content: '';
  display: block;
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  height: calc(100% - 70px);
  pointer-events: auto;
}

h2 a {
  opacity: 0;
  pointer-events: auto;
}

h2:hover a {
  opacity: 1;
}
```

`h2:after` の部分がポイントですね。 `height: calc(100% - 70px)` によって「 `h2` 全体から見えないマージンの高さを除いた高さ」をセットすることで、結果的にテキスト部分と同じ高さを作っています👍

> 見出しのテキストが常に1行に収まるのなら `height: 1em` とかでも結果は変わらないですが、見出しのテキストが何行になってもテキスト全体がhoverの対象になるようにするには、この方法しかないと思います。

👉というわけで、最終的なCSSの例は [こちら](#_2-見出しタグのマークアップをいじれない場合) のようになります。

# 余談

ちなみに、このブログのテーマとしても利用している、拙作 [vuepress-theme-blog-vuetify](https://github.com/ttskch/vuepress-theme-blog-vuetify/) もこの方法で見出しタグをスタイリングしています。

以下のデモページで実際の挙動を確認できますので、興味のある方は覗いてみてください✨

<https://vuepress-theme-blog-vuetify.ttskch.com/2020/04/01/post1/>

以上、参考になれば幸いです！
