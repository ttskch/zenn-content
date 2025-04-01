---
title: "Symfony UX Autocomplete（というかTom Select）でreadonlyを実現する（超簡単）"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "form", "tomselect"]
published: true
---

# 課題

**誰もが一度は `<select>` タグに `readonly` 属性が欲しいと思ったことがあるはずです。**

`disabled` 属性を付与すれば操作できなくすることはできますが、それだとフォーム送信時に値が送られなくなってしまうという問題があります。

![](https://img.esa.io/uploads/production/attachments/15064/2025/04/01/77821/a81f6a0f-59e0-4f81-9290-8476a9309795.png)

![](https://img.esa.io/uploads/production/attachments/15064/2025/04/01/77821/cb97699e-f1e7-4e29-8270-a33ae865f995.png)

[「顧客が本当に必要だったもの」ことSymfony UX Autocomplete](https://zenn.dev/ttskch/articles/dc3c09b71f73d4) では、フロントエンド側の実装に [Tom Select](https://tom-select.js.org/) というライブラリが使用されていますが、Tom Selectでは `readonly` 的な挙動を実現するための `lock()` というメソッドが提供されています。

https://tom-select.js.org/examples/lock/

が、Symfony UX Autocomplete経由でTom Selectを使用するケースでは、すでにSymfony UX Autocomplete経由で初期化されたTom Selectインスタンスをわざわざ自前のJSのコードで取得して `lock()` を叩く、みたいな冗長なことはできればやりたくありません。

希望としては、以下のように `data-readonly` 属性とかを付与しておくだけで、自動で `readonly` 的な挙動になってほしいところです。

```php
$builder
    ->add('foo', ChoiceType::class, [
        'autocomplete' => true,
        'attr' => [
            'data-readonly' => true,
        ],
        'choices' => ['選択肢1' => '選択肢1'],
        'data' => '選択肢1',
    ])
;
```

# 解決方法

結論としては、以下のCSSを定義しておくだけで実現できます。

```css
select[data-readonly] + .ts-wrapper {
  background-color: #fff;
  opacity: 0.5;
  pointer-events: none;
}
```

見た目を薄くして `pointer-events: none` でクリックできなくしただけですが、これで要求は完全に満たされます。発想の転換ですね。

![](https://img.esa.io/uploads/production/attachments/15064/2025/04/01/77821/cbd97690-9dda-43de-8c91-da989d0e3104.gif)

# おまけ

CSSで誤魔化さず、ちゃんとTom Selectの `lock()` を効かせたい場合は、[Tom Selectを拡張する用のカスタムStimulusコントローラー](https://symfony.com/bundles/ux-autocomplete/current/index.html#extending-tom-select) を作成した上で、`_onPreConnect` に以下のようなコードを書けば実現できます。

```js:assets/controllers/custom-autocomplete_controller.js
if (event.srcElement.hasAttribute('data-readonly')) {
  event.detail.options.onInitialize = () =>
    event.srcElement.tomselect.lock()
}
```

ただ、これだと下図のように **「触ることはできるのになぜか入力値を変更できない」という印象** の挙動になってしまうので、この場合でもCSSで見た目を薄くしてクリックできなくする対応はあわせて行ったほうが無難だと個人的には思います。

![](https://img.esa.io/uploads/production/attachments/15064/2025/04/01/77821/b9fd30dc-b38c-4902-bcea-561071721604.gif)

# おまけのおまけ

カスタムStimulusコントローラーを作らずにグローバルなJSから直接処理しようとすると、Tom Selectの初期化処理が完了したタイミングを知ることができないため、以下のような最悪のコードを書かないといけなくなるので気をつけましょう。

```js:assets/app.js
setTimeout(
  () =>
    document
      .querySelectorAll('select[data-readonly]')
      .forEach((el) => el.tomselect.lock()),
  500,
)
```