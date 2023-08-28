---
title: "CSS GridでヘッダーだけをStickkにする方法"
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
  min-height: 100vh;
}
```

この状態からさらに`header` だけを `position: sticky;` 相当にしたい場合は、以下のようにすればよいというお話です。

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
  height: 100vh;
}

#wrapper {
  display: grid;
  grid-template-rows: 1fr auto;
  overflow: auto;
}
```

動作例はこちら。

@[codepen](https://codepen.io/ttskch/pen/eYbJWYg)
