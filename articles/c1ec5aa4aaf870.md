---
title: "[JavaScript] オブジェクトを値でソートする"
emoji: "💻"
type: "tech"
topics: ["javascript"]
published: true
published_at: 2020-05-08
---

:::message
この記事は、2020-05-08に別のブログ媒体に投稿した記事のアーカイブです。
:::

例えばこんなオブジェクトがあるとします。

```js
{
  key1: 5,
  key2: 30,
  key3: 7,
  key4: 18,
  key5: 10,
}
```

これを

```js
{
  key1: 5,
  key3: 7,
  key5: 10,
  key4: 18,
  key2: 30,
}
```

という具合に値の昇順でソートしたいと思ったとき、どうすればいいでしょうか？

> ちなみにPHPなら [ksort](https://www.php.net/manual/ja/function.ksort.php) で一発ですね。

# オブジェクト自身をソートはできない

実は、上に挙げた例のようなことは物理的にできません😅

JavaScriptの仕様として、オブジェクトにおけるプロパティの順序は何ら保証されていないためです。（[参考](https://stackoverflow.com/questions/5525795/does-javascript-guarantee-object-property-order)）

なので、一旦

```js
[
  { key: 'key1', value: 5 },
  { key: 'key2', value: 30 },
  { key: 'key3', value: 7 },
  { key: 'key4', value: 18 },
  { key: 'key5', value: 10 },
]
```

のような配列に整形してから、

```js
[
  { key: 'key1', value: 5 },
  { key: 'key3', value: 7 },
  { key: 'key5', value: 10 },
  { key: 'key4', value: 18 },
  { key: 'key2', value: 30 },
]
```

のようなソートされた配列を得ることをゴールとするのがセオリーです。

# ステップ1：配列にする

では実際のやり方を説明します。

すべて自力でやるのは大変なので、[lodash](https://lodash.com/) を使わせてください🙏

まず、

```js
{
  key1: 5,
  key2: 30,
  key3: 7,
  key4: 18,
  key5: 10,
}
```

これを

```js
[
  { key: 'key1', value: 5 },
  { key: 'key2', value: 30 },
  { key: 'key3', value: 7 },
  { key: 'key4', value: 18 },
  { key: 'key5', value: 10 },
]
```

こうすることを考えてみましょう。

lodashの [transform](https://lodash.com/docs/4.17.15#transform) を使ってこんな感じのコードで実現できます。

```js
const object = {
  key1: 5,
  key2: 30,
  key3: 7,
  key4: 18,
  key5: 10,
}

const array = _.transform(object, (result, value, key) => {
  result.push({ key: key, value: value })
}, [])
```

動作例：<https://jsfiddle.net/ttskch/5ab79dsf/>

# ステップ2：配列をプロパティでソートする

次に、

```js
[
  { key: 'key1', value: 5 },
  { key: 'key2', value: 30 },
  { key: 'key3', value: 7 },
  { key: 'key4', value: 18 },
  { key: 'key5', value: 10 },
]
```

これを

```js
[
  { key: 'key1', value: 5 },
  { key: 'key3', value: 7 },
  { key: 'key5', value: 10 },
  { key: 'key4', value: 18 },
  { key: 'key2', value: 30 },
]
```

こうします。

つまり、オブジェクトの配列を `value` というプロパティでソートすればいいわけですね。

lodashの [sortBy](https://lodash.com/docs/4.17.15#sortBy) を使えば一撃です。

```js
const array = [
  { key: 'key1', value: 5 },
  { key: 'key2', value: 30 },
  { key: 'key3', value: 7 },
  { key: 'key4', value: 18 },
  { key: 'key5', value: 10 },
]

const sortedArray = _.sortBy(array, 'value')
```

動作例：<https://jsfiddle.net/ttskch/6vnmgoLq/>

# ステップ3：chainで繋げる

ステップ1とステップ2を合体させると以下のようなコードになります。

```js
const object = {
  key1: 5,
  key2: 30,
  key3: 7,
  key4: 18,
  key5: 10,
}

const sortedArray = _.sortBy(_.transform(object, (result, value, key) => {
  result.push({ key: key, value: value })
}, []), 'value')
```

一応これで実装できたことはできたのですが、あまり読みやすいコードとは言えませんね。

こういうときのために、lodashには [chain](https://lodash.com/docs/4.17.15#chain) という便利なメソッドがあります。これを使うとlodashの各種メソッドを連続して使いたいときにメソッドチェーンで記述することができます。

上記のコードは `chain` を使うと以下のように書き換えられます。

```js
const sortedArray = _.chain(object).transform((result, value, key) => {
  result.push({ key: key, value: value })
}, []).sortBy('value').value()
```

`_.chain(object)` でラッパーを作成して、そこから先は `.transform()` `.sortBy()` とメソッドチェーンを繋いで、全部終わったら最後に `.value()` でラッパーから値の実体を取り出して完了です。

ずいぶん読みやすくなりましたよね。

ちなみに、昇順ではなく降順で取得したい場合は、[reverse](https://lodash.com/docs/4.17.15#reverse) を噛ませればOKです。

```js
const sortedArray = _.chain(object).transform((result, value, key) => {
  result.push({ key: key, value: value })
}, []).sortBy('value').reverse().value()
```

簡単ですね！

動作例：<https://jsfiddle.net/ttskch/kjawyrL4/>

# おまけ：値が重複して出現する配列において、値を出現頻度の高い順でソートして取得する

例えば、

* `投稿` が `タグ` の配列を持っている
* `投稿` がたくさんある
* すべての `タグ` の配列を、多くの `投稿` で使われている順にソートして取得したい

というケースを考えてみましょう。

```js
const posts = [
  { tags: ['5times'] },
  { tags: ['5times', '4times'] },
  { tags: ['5times', '4times', '3times'] },
  { tags: ['5times', '4times', '3times', '2times'] },
  { tags: ['5times', '4times', '3times', '2times', '1time'] },
]
```

簡易化していますがこんな感じで `投稿` の配列があり、これを元に

```js
const hotTags = ['5times', '4times', '3times', '2times', '1time']
```

という配列を得たい、というのがゴールです。

以下のようなコードで実現できます。

```js
const posts = [
  { tags: ['5times'] },
  { tags: ['5times', '4times'] },
  { tags: ['5times', '4times', '3times'] },
  { tags: ['5times', '4times', '3times', '2times'] },
  { tags: ['5times', '4times', '3times', '2times', '1time'] },
]

let tags = []
posts.forEach(post => {
  tags = _.concat(tags, post.tags)
})

const hotTags = _.chain(_.countBy(tags)).map((count, tag) => {
  return {tag: tag, count: count}
}).sortBy('count').reverse().map('tag').value()
```

まず初めに、すべての `投稿` から `タグ` を取り出して、値が重複して出現する配列 `tags` を作っています。

この時点で `tags` の中身は

```
["5times", "5times", "4times", "5times", "4times", "3times", "5times", "4times", "3times", "2times", "5times", "4times", "3times", "2times", "1time"]
```

こんな感じです。

次に、上記の `tags` を [countBy](https://lodash.com/docs/4.17.15#countBy) に掛けてそれぞれの値と出現数の情報を持った以下のようなオブジェクトを作ります。

```js
{
  1time: 1,
  2times: 2,
  3times: 3,
  4times: 4,
  5times: 5
}
```

これを [map](https://lodash.com/docs/4.17.15#map) で `{ tag: 値, count: 出現数 }` というオブジェクトの配列に整形します。

あとはこれを [sortBy](https://lodash.com/docs/4.17.15#sortBy) で出現数順にソートして、[reverse](https://lodash.com/docs/4.17.15#reverse) で降順にして、[map](https://lodash.com/docs/4.17.15#map) で「値だけの配列」に変換して終わりです。

やや難しいですが、やってることが理解できれば自分でも書けそうですよね💪

動作例：<https://jsfiddle.net/ttskch/5r4bun1w/>

# 参考サイト

* <https://stackoverflow.com/questions/28992056/sorting-the-results-of-countby>
* <https://stackoverflow.com/questions/28354725/lodash-get-an-array-of-values-from-an-array-of-object-properties>
