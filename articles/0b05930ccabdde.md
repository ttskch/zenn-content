---
title: "CSS GridでヘッダーだけをStickyにする方法"
emoji: "🚀"
type: "tech"
topics: ["css"]
published: true
---

メモです。

* `header`
* `main`
* `footer`

の3行構成のページで、`main` の内容が少ないときでも `footer` が画面の最下部に来るようにしたい場合、CSS Gridを使って以下のように書くことができます。

```html
<div id="grid">
  <header>header</header>
  <main>main</main>
  <footer>footer</footer>
</div>
```

```css
#grid {
  display: grid;
  grid-template-rows: auto 1fr auto;
  min-height: 100dvh;
}
```

この状態からさらに`header` だけを `position: sticky;` 相当にしたい場合は、以下のようにすればよいというお話です。

# 結論

もともとは次項の不完全な内容を紹介してたのですが、よくよく考えたら普通に以下の方法で対応可能だったので、結果的になんの工夫もない普通の方法をご紹介する記事になってしまいました🙄

```html
<div id="grid">
  <header>header</header>
  <main>main</main>
  <footer>footer</footer>
</div>
```

```css
#grid {
  display: grid;
  grid-template-rows: auto 1fr auto;
  min-height: 100dvh;
}

/* 以下を追記したのみ */
header {
  position: sticky;
  top: 0;
  z-index: 999;
}
```

動作例はこちら。

@[codepen](https://codepen.io/ttskch/pen/VYZWjjq)

# 不完全な方法

実は記事公開当時は以下の内容でご紹介していたのですが、よくよく考えたら普通に上記の方法で対応可能だったのと、それどころか以下の方法だとiPhoneで画面上端をタップしてもページトップにスクロールする挙動が起こらないという致命的な問題があることが分かった（2024年12月現在）ので、以下の内容はあまり参照する意味のない陳腐化した情報です。一応記録のために残すだけ残しておきます🙏

:::details 不完全な方法
```html
<div id="grid">
  <header>header</header>
  <div id="wrapeer">
    <main>main</main>
    <footer>footer</footer>
  </div>
</div>
```

```css
#grid {
  display: grid;
  grid-template-rows: auto 1fr;
  height: 100dvh;
}

#wrapper {
  display: grid;
  grid-template-rows: 1fr auto;
  overflow: auto;
}
```

動作例はこちら。

@[codepen](https://codepen.io/ttskch/pen/eYbJWYg)
:::

# History APIでのブラウザバック時にスクロール位置が復元しない場合の対処

2024年9月現在、Next.js 制のアプリでこの実装を使ったところ、`next.config.js` で  `experimental: { scrollRestoration: true }` を設定しているにもかかわらずブラウザバックでスクロール位置が復元しない挙動になってしまいました。

調べたところ、History APIの `scrollRestoration` が、ページ全体のスクロール位置の復元しかできない（今回のような実装で `main` の中身だけが `hidden: scroll` によってスクロールされたその位置までは復元できない）仕様のようです。

> 参考：[History API でもページバック時にスクロールバーの位置は復元される `#復元できないケース`](http://var.blog.jp/archives/84930940.html#%E5%BE%A9%E5%85%83%E3%81%A7%E3%81%8D%E3%81%AA%E3%81%84%E3%82%B1%E3%83%BC%E3%82%B9)

というわけで、もしこれが問題になる場合は、CSS Gridを使うのは諦めるしかなくて、例えば以下のような実装で代替できるかと思います。

```html
<div id="wrapper">
  <header>header</header>
  <main>
    <div>
      <button id="short">short content</button>
      <button id="long">long content</button>
    </div>
    <div id="content">short content</div>
    <br><br>
  </main>
  <footer>footer</footer>
</div>
```

```css
#wrapper {
  min-height: 100dvh;
}

header {
  position: sticky;
  top: 0;
}

footer {
  position: sticky;
  top: 100dvh;
}
```

動作例はこちら。

@[codepen](https://codepen.io/ttskch/pen/PoXemXO)

> 参考：[この実装方法は巧い！ コンテンツが少ない量でもフッタを一番下に配置するCSSのテクニック | コリス](https://coliss.com/articles/build-websites/operation/css/clever-sticky-footer-technique.html)
