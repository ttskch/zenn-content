---
title: "nelmio/aliceのcurrentの使い方まとめ"
emoji: "🤖"
type: "tech"
topics: ["php"]
published: true
---

# nelmio/aliceのcurrent

[nelmio/alice](https://github.com/nelmio/alice) を使ってYAML形式でテストデータを記述する際、[`<current()>`](https://github.com/nelmio/alice/blob/main/doc/customizing-data-generation.md#current) という組み込み関数を使うと繰り返しをシンプルに記述できて便利です。

```yaml
App\Entity\Foo:
  foo{1..2}:
    currentValue: <current()> # '1', '2'
```

このように単純にインデックスの値をそのまま使いたい場合はよいのですが、インデックスの値を何かしら加工して使いたい場合には少し工夫が必要です。

# intにキャストする

`<current()>` 関数は現在のインデックスを [文字列として返す](https://github.com/nelmio/alice/blob/e59ce4439b7d98d167ac15c46e25aa7d1eff5273/src/Faker/Provider/AliceProvider.php#L30-L36) ので、`int` しか渡せないプロパティなどに使う場合は適切にキャストする必要があります。

`nelmio/alice` には [`<intval()>` という組み込み関数](https://github.com/nelmio/alice/blob/main/doc/customizing-data-generation.md#cast) があるので、以下のようにしてキャストできそうな気がしますが、**実はこれは期待どおりに機能しません。**

```yaml
App\Entity\Foo:
  foo{1..2}:
    currentValueWithInt: <intval(current())> # 0, 0
```

この書き方だと `<intval()>` 関数の引数に `<current()>` 関数の結果を渡していることにはなっておらず、常に `0` が返ってきてしまいます。（詳しくコード追えていませんが、関数をネストするという使い方はできないということのようです）

期待どおりに機能させるには、以下のように書きます。

```yaml
App\Entity\Foo:
  foo{1..2}:
    currentValueWithInt: <(intval($current))> # 1, 2
```

`<()>` の中に `intval($current)` が入っています。

この `<()>` は [`<identity()>` という組み込み関数](https://github.com/nelmio/alice/blob/main/doc/advanced-guide.md#identity) のショートハンドで、引数として渡したものをPHPコードとして評価してくれるというものです。

なので、ここで引数に渡している `intval($current)` は、`<intval()>` 関数ではなく、PHPの `intval()` 関数というわけです。

`$current` は、[`<identity()>` 関数の説明にもあるとおり](https://github.com/nelmio/alice/blob/main/doc/advanced-guide.md#identity) `<current()>` 関数の戻り値と同じ値にアクセスできる組み込み変数です。

# インデックスをずらして使う

`<()>` さえあればもはや何でもできますね。

例えば、`{3..4}` と付けたインデックスを、`1` `2` として使用したいような場合は、以下のように書けばよいです。

```yaml
App\Entity\Foo:
  foo{1..2}:
    currentValue: <current()>

App\Entity\Bar:
  bar{1..2}:
    state: valid
    foo: '@bar<current()>' # '@bar1', '@bar2'
  bar{3..4}:
    state: invalid
    foo: '@bar<($current-2)>' # '@bar1', '@bar2'
```

# インデックスの値をDateTimeなどに使う

さらに、`DateTime` などの日時の値にインデックスを使用するのも簡単です。

```yaml
App\Entity\Foo:
  foo{1..2}:
    createdAt: <(new \DateTime("2024-$current-01"))> # \DateTime('2024-1-01'), \DateTime('2024-2-01')
```

ずらして使う場合はこんな感じです。

```yaml
App\Entity\Foo:
  foo{3..4}:
    createdAt: <(new \DateTime(sprintf('2024-%02d-01', $current-2)))> # \DateTime('2024-01-01'), \DateTime('2024-02-01')
```

# 参考リンク

* [nelmio/aliceでcurrentの値をintegerとして扱う方法について](https://polidog.jp/2023/07/11/nelmio-alice/)
