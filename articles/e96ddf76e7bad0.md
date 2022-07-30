---
title: "[Bootstrap] 一覧画面の検索フォームのレイアウト例"
emoji: "💻"
type: "tech"
topics: ["bootstrap"]
published: true
published_at: 2020-07-04
---

:::message
この記事は、2020-07-04に別のブログ媒体に投稿した記事のアーカイブです。
:::

小ネタです。

Webアプリで何かしらの一覧画面に検索フォームを設置することはよくあると思いますが、要件によってはこの検索フォームが結構複雑になったり入力項目が多くなったりすることもまあありますよね。

一項目だけのシンプルな検索フォームなら

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ggf0czxnhqj323u070q3h.jpg)

こんな感じで特に何も考えることはないのですが、項目が多くなってくると1行に詰め込むのが難しくなってきたり、レスポンシブにしようとすると意外ときれいにレイアウトするのが難しかったりします。

というわけで、僕は最近はだいたいこんな感じでレイアウトしてますという実装例をご紹介したいと思います。Bootstrapを使った例です。

# 完成形の例

こんな感じです。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ggf08tqln5g327s06lx6z.gif)

項目が増えても行を増やしてそれなりにきれいな見た目を維持できる感じになっているかなーと思います。

# 実際のコード

上の例の実装コードは以下のような感じになっています。

```html
<form action="/path/to/action" method="get" class="form-inline align-items-start">
  <div class="container ml-0 mb-3">
    <div class="row d-flex mb-2">
      <div class="flex-grow-1">
        <input type="search" name="query" placeholder="全文検索" class="form-control w-100">
      </div>
      <button type="submit" class="btn btn-outline-secondary ml-2"><i class="fa fa-search"></i></button>
    </div>
    <div class="row d-flex" style="margin-right:29px">
      <div class="mr-2 flex-grow-1" style="width:1px">
        <select name="axis1[]" class="form-control">
          <option value="ほげ">ほげ</option>
          <option value="ふが">ふが</option>
          <option value="ぴよ">ぴよ</option>
        </select>
      </div>
      <div class="flex-grow-1" style="width:1px">
        <select name="axis2[]" class="form-control">
          <option value="foo">foo</option>
          <option value="bar">bar</option>
          <option value="baz">baz</option>
        </select>
      </div>
    </div>
  </div>
</form>
```

> キャプチャの例では、 `<select>` タグには [select2](https://select2.org/) が適用されています。

あまりきれいなコードではないですが、レンダリング結果がそれなりに美しくなることを優先して、一旦これ以上は頑張れていません😅

以下あたりがポイントかなと思います。

* 全体を `container ml-0` で囲って、大画面でも無意味に横いっぱいに大きくしないようにしている
* 横いっぱいフルに使う行と2分割にする行があっても、検索ボタンだけが右に飛び出した状態になるように端を揃えている
    * `margin-right:29px` をハードコードしちゃってるのでやり方としてはすごくイマイチです🙏（もっといいやり方あるよという方がいたらぜひ [DM](https://twitter.com/ttskch) ください）
* 2分割の行ではそれぞれの列に `width:1px` と `flex-grow-1` をつけることで、同じ幅を保ったまま伸び縮みするようにしている
    * `1px` でなくても同じサイズを指定していればOKです

何かの参考になれば幸いです💡
