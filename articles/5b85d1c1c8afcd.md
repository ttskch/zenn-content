---
title: "[Symfony][Form] ChoiceType（およびEntityType）の基本的な使い方"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony"]
published: true
published_at: 2020-03-07
---

:::message
この記事は、2020-03-07に別のブログ媒体に投稿した記事のアーカイブです。
:::

symfony/formのCollectionType（およびEntityType）の基本的な使い方についてまとめてみます。

# ChoiceTypeとは

「選択肢の中から1つまたは複数を選択させるフォーム要素」を司るのがChoiceTypeです。

具体的には、

* `<select>`
* `<select multiple>`
* `<input type="radio">`
* `<input type="checkbox">`

の4つを作ることができます。

# `select` `select(multiple)` `radio` `checkbox` の出し分け方

出し分け方はとてもシンプルで、

* `expanded` （展開表示するかどうか）
* `multiple` （複数選択可能かどうか）

という2つのオプションをそれぞれ `true` `false` どちらに設定するかによって決まります。

| 出力されるタグ            | `expanded` の値 | `multiple` の値 | つまり                       |
| ------------------------- | --------------- | --------------- | ---------------------------- |
| `<select>`                | `false`         | `false`         | 複数選択不可・展開せずに表示 |
| `<select multiple>`       | `false`         | `true`          | 複数選択不可・展開して表示   |
| `<input type="radio">`    | `true`          | `false`         | 複数選択可・展開せずに表示   |
| `<input type="checkbox">` | `true`          | `true`          | 複数選択可・展開して表示     |

`expanded` も `multiple` もデフォルト値は `false` なので、何も指定しなければ `<select>` タグが出力されます。

## selectの例

```php
$builder->add('category', ChoiceType::class, [
    'choices'  => [
        'Category A' => 'a',
        'Category B' => 'b',
        'Category C' => 'c',
    ],
]);
```

* 必須のオプションは `choices` のみ
* `choices` に渡す連想配列は `[ラベル => 値]` の形

です。レンダリング結果のHTMLは以下のようになります。

```html
<select id="form_category" name="form[category]">
    <option value="a">Category A</option>
    <option value="b">Category B</option>
    <option value="c">Category C</option>
</select>
```

![](https://tva1.sinaimg.cn/large/00831rSTgy1gcl2udzj13j30ro07awh2.jpg)

> キャプチャ画像は [ttskch/symfony-micro-skeleton](https://github.com/ttskch/symfony-micro-skeleton) をベースに動かしたものなので、Bootstrap4のスタイルが適用されています。

## select(multiple)の例

`'multiple' => true` をセットするだけでOKです。

```php
$builder->add('category', ChoiceType::class, [
    'choices'  => [
        'Category A' => 'a',
        'Category B' => 'b',
        'Category C' => 'c',
    ],
    'multiple' => true,
]);
```

```html
<select id="form_category" name="form[category][]" required="required" multiple="multiple">
    <option value="a">Category A</option>
    <option value="b">Category B</option>
    <option value="c">Category C</option>
</select>
```

![](https://tva1.sinaimg.cn/large/00831rSTgy1gcl2e55wf3j30rq07ogm3.jpg)

## radioの例

`'expanded' => true` をセットするだけでOKです。

```php
$builder->add('category', ChoiceType::class, [
    'choices'  => [
        'Category A' => 'a',
        'Category B' => 'b',
        'Category C' => 'c',
    ],
    'expanded' => true,
]);
```

```html
<input type="radio" id="form_category_0" name="form[category]" required="required" value="a">
<label for="form_category_0">Category A</label>

<input type="radio" id="form_category_1" name="form[category]" required="required" value="b">
<label for="form_category_1">Category B</label>

<input type="radio" id="form_category_2" name="form[category]" required="required" value="c">
<label for="form_category_2">Category C</label>
```

![](https://tva1.sinaimg.cn/large/00831rSTgy1gcl2d133u0j30rc06mwex.jpg)

## checkboxの例

`'expanded' => true` と `'multiple' => true` を両方セットします。

```php
$builder->add('category', ChoiceType::class, [
    'choices'  => [
        'Category A' => 'a',
        'Category B' => 'b',
        'Category C' => 'c',
    ],
    'expanded' => true,
    'multiple' => true,
]);
```

```html
<input type="radio" id="form_category_0" name="form[category]" required="required" value="a">
<label for="form_category_0">Category A</label>

<input type="radio" id="form_category_1" name="form[category]" required="required" value="b">
<label for="form_category_1">Category B</label>

<input type="radio" id="form_category_2" name="form[category]" required="required" value="c">
<label for="form_category_2">Category C</label>
```

![](https://tva1.sinaimg.cn/large/00831rSTgy1gcl2rkh71mj30r806m3yy.jpg)

# よく使うオプション

## [`placeholder`](https://symfony.com/doc/current/reference/forms/types/choice.html#placeholder)

出力形式が「multipleでない `<select>` タグ」な場合にのみ使える、「値のない選択肢（プレースホルダー）を出力する」ためのオプションです。

```php
$builder->add('category', ChoiceType::class, [
    'choices'  => [
        'Category A' => 'a',
        'Category B' => 'b',
        'Category C' => 'c',
    ],
    'placeholder' => true,
]);
```

```html
<select id="form_category" name="form[category]" required="required">
    <option value="" selected="selected">選択してください</option>
    <option value="a">Category A</option>
    <option value="b">Category B</option>
    <option value="c">Category C</option>
</select>
```

![](https://tva1.sinaimg.cn/large/00831rSTgy1gcl2xmm6qkj30rm0540sz.jpg)

## [`choice_attr`](https://symfony.com/doc/current/reference/forms/types/choice.html#choice-attr)

一つひとつの選択肢について、HTML出力時に付加する属性をセットするオプションです。

```php
$builder->add('category', ChoiceType::class, [
    'choices'  => [
        'Category A' => 'a',
        'Category B' => 'b',
        'Category C' => 'c',
    ],
    'choice_attr' => [
        'Category A' => [
            'class' => 'category-choice category-choice-a',
        ],
        'Category B' => [
            'class' => 'category-choice category-choice-b',
        ],
        'Category C' => [
            'class' => 'category-choice category-choice-c',
        ],
    ],
]);
```

```html
<select id="form_category" name="form[category]">
    <option value="a" class="category-choice category-choice-a">Category A</option>
    <option value="b" class="category-choice category-choice-b">Category B</option>
    <option value="c" class="category-choice category-choice-c">Category C</option>
</select>
```

上記のように連想配列で値をセットする場合は、 `choices` にセットした連想配列と同じキーにする必要があります。

また、連想配列ではなくクロージャーを渡すことでより動的な形で属性をセットすることもできます。

```php
$builder->add('category', ChoiceType::class, [
    'choices'  => [
        'Category A' => 'a',
        'Category B' => 'b',
        'Category C' => 'c',
    ],
    'choice_attr' => function($choice, $key, $value) {
        return [
            'class' => 'category-choice category-choice-'.strtolower($value),
        ];
    },
]);
```

このコードは、先ほどの連想配列型式の例とまったく同一の結果になります。

クロージャーの引数には

* `$choice` ：各選択肢の実体
* `$key` ：各選択肢の `choices` オプションにおけるキー（ここでは `Category A` `Category B` `Category C` ）
* `$value` ：各選択肢の `choices` オプションにおける値（ここでは `a` `b` `c` ）

が渡されるので、いろいろな活用方法がありそうですね。

# EntityTypeはChoiceTypeの拡張

[公式ドキュメントにも書いてあるとおり](https://symfony.com/doc/current/reference/forms/types/entity.html)、EntityTypeはChoiceTypeの拡張実装です。

なので、ChoiceTypeのオプションが基本的にそのまま使えます。

## EntityTypeの基本的な使い方

```php
$builder->add('users', EntityType::class, [
    'class' => User::class,
    // 'multiple' => true,
    // 'expanded' => true,
]);
```

EntityTypeで必須となるオプションは `class` のみです。 `'class' => User::class` のように、「どのエンティティを対象とするか」をエンティティのクラス名で指定します。

上記の例では `multiple` と `expanded` をコメントアウトしてありますが、これらのオプションはChoiceTypeのときとまったく同じように使えます。

## EntityTypeで `choice_attr` を使うとエンティティの情報を簡単にフロントエンドに渡せる

`choice_attr` オプションを以下のように使うことで、エンティティ固有の情報を各choiceに持たせて、フロントエンド側で活用できます。

```php
$builder->add('users', EntityType::class, [
    'class' => User::class,
    'choice_attr' => function(User $choice, $key, $value) {
        return [
            'data-created-at' => $choice->getCreatedAt()->format('Y-m-d'),
            'data-updated-at' => $choice->getUpdatedAt()->format('Y-m-d'),
        ];
    },
]);
```

```js
$('select option').each(function () {
    console.log($(this).data('created-at'));
    console.log($(this).data('updated-at'));
});
```

便利なので覚えておくといいかもしれません👍

## `query_builder` オプションとForm Events

EntityTypeの `query_builder` オプションやForm Eventsといった機能を活用するとさらに細かい細工ができます。

<https://zenn.dev/ttskch/articles/81e4e46378a87b>

こちらの記事に詳しくまとめてありますので、興味があれば覗いてみてください。

# 参考URL

ChoiceType/EntityTypeのより細かい機能については公式ドキュメントをご参照ください。

* <https://symfony.com/doc/current/reference/forms/types/choice.html>
* <https://symfony.com/doc/current/reference/forms/types/entity.html>

# まとめ

* ChoiceTypeの基本的な使い方についてまとめてみました
* EntityTypeはChoiceTypeの拡張なので、ChoiceTypeと同じ感覚で扱えます
