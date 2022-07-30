---
title: "[5分で解決] esaに大きな画像を貼ったときに画面が占有されて見づらい問題"
emoji: "📝"
type: "idea"
topics: ["esa", "chrome"]
published: true
published_at: 2020-05-21
---

:::message
この記事は、2020-05-21に別のブログ媒体に投稿した記事のアーカイブです。
:::

# esaに大きな画像を貼ったときに画面が占有されて見づらい問題

こういうのです。地味にストレス溜まりますよね。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1gezrvrkhlng31co0od4qx.gif)

# 簡単な解決策

`<img>` に `max-height: 30vh;` とかを指定するだけで簡単に解決できます✨

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1gezrz3kuisg31co0odqv5.gif)

指定したWebページに任意のCSSを適用できるブラウザ拡張は探せばたくさんあるので、それを使いましょう。

僕の場合はGoogle Chromeの [Animo](https://chrome.google.com/webstore/detail/amino-live-css-editor/pbcpfbcibpcbfbmddogfhcijfpboeaaf/) という拡張を使っています。

Animoをインストールしたら、esaを開いてからAnimoのポップアップウィンドウを開きましょう。

下図のように `Page` `Domain` とタブが分かれているので `Domain` のほうに切り替えて、適用したいCSSを書いて `Save` すればすぐに画面に反映されます👍

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1gf23pd95ktj30qu0xywg0.jpg)

僕が使っているCSSは以下のとおり。

```css
.post-body.markdown img,
#preview-body .markdown img {
    max-height: 30vh;
    width: auto;
}
```

記事ページの本文中の `<img>` と、記事編集画面のプレビュー領域中の `<img>` の両方に、 `max-height: 30vh; width: auto;` を設定しているだけです。

`30vh` の値はお好みで適当に変えてみてください✋

> もちろん、esaのマークアップに変更があれば都度追従する必要がありますのでご注意を。

# まとめ

たったこれだけですごく快適になるので、ぜひお試しアレ！
