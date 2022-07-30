---
title: "[Symfony] EasyAdminのフォームフィールドにStimulusの処理を当てる"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "easyadmin", "javascript", "stimulus"]
published: true
published_at: 2022-01-27
---

:::message
この記事は、2022-01-27に別のブログ媒体に投稿した記事のアーカイブです。
:::

# やりたかったこと

* [EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html) で出力されるフォームフィールドに、[Webpack Encoreに統合されているStimulus](https://symfony.com/doc/current/frontend/encore/simple-example.html#stimulus-symfony-ux) で書いたJSの処理を適用したかった

# やり方

## 0. Stimulusについておさらい

[`symfony/webpack-encore-bundle`](https://github.com/symfony/webpack-encore-bundle) の [レシピ](https://github.com/symfony/recipes/blob/master/symfony/webpack-encore-bundle/1.9) を実行すると、[`@hotwired/stimulus`](https://github.com/hotwired/stimulus) や [`@symfony/stimulus-bridge`](https://github.com/symfony/stimulus-bridge) を依存に含む [`package.json`](https://github.com/symfony/recipes/blob/master/symfony/webpack-encore-bundle/1.9/package.json) が展開され、Stimulusを使って書いたJSの処理をSymfonyアプリに簡単に統合できるようになっています。

> Stimulus自体についての細かい説明はこの記事では割愛しますが、ググれば丁寧な解説が色々見つかると思います！🙏

`symfony/webpack-encore-bundle` のレシピを実行した時点で、

* `assets/app.js`
* `assets/bootstrap.js`
* `assets/controllers.json`
* `assets/contollers/hello_controller.js`

などのStimulus関係のファイルが一式作成されており、`webpack.config.js` に

```js
.addEntry('app', './assets/app.js')
.enableStimulusBridge('./assets/controllers.json')
```

この2行が記載済みになっているため、HTML（Twig）側で

* `app` エントリーのアセットを読み込んで
*  適当なHTML要素に `data-controller="hello"` を付加する

だけで、その要素に [`assets/controllers/hello_controller.js` の処理](https://github.com/symfony/recipes/blob/master/symfony/webpack-encore-bundle/1.9/assets/controllers/hello_controller.js) が適用されて、

```
Hello Stimulus! Edit me in assets/controllers/hello_controller.js
```

というインナーテキストが表示されます👌

## 1. Stimulusのコントローラを実装

というわけで、実際にEasyAdminのフォームフィールドに適用したい処理を書いたStimulusのコントローラを実装すれば、あとは `data-controller="コントローラ名"` を付与するだけでよいと分かりました。

今回は具体例として、**郵便番号が入力されたらAPIから住所を取得して住所入力欄を補完する** という処理を書いてみましょう。

```js
// assets/controllers/zipCode_controller.js

import {Controller} from '@hotwired/stimulus'
import axios from 'axios'

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  connect() {
    const addressInput = this.element
      .closest('form')
      .querySelector('[name$="[address]"]')

    if (!addressInput) {
      return
    }

    this.element.addEventListener('blur', () => {
      addressInput.disabled = true
      ;(async () => {
        try {
          const res = await axios.get(`/some/api?zipCode=${this.element.value}`)
          addressInput.value = res.data
        } finally {
          addressInput.disabled = false
        }
      })()
    })
  }
}
```

こんな感じで実装してみました。

> `/* stimulusFetch: 'lazy' */` というコメントは、自分を `data-controller` 属性で指定している要素がいないページではJSファイルが読み込まれなくなるためのオプションです。
>
> ドキュメント：<https://github.com/symfony/stimulus-bridge#lazy-controllers>

## 2. EasyAdminのFieldにStimulusコントローラの処理を適用

あとはこのStimulusコントローラをEasyAdminのFieldに当てがうだけです。

```php
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FooCrudController extends AbstractCrudController
{
    // ...
    
    public function configureFields(string $pageName): iterable
    {
        // ...
        
        yield Field::new('zipCode')
            ->setFormTypeOption('attr', [
                'data-controller' => 'zipCode',
            ])
            ->addWebpackEncoreEntries('app')
        ;
        
        yield Field::new('address');
        
        // ...
    }
}
```

* `setFormTypeOption(/* 略 */)` でフォームフィールドに `data-controller="zipCode"` を付加
* `addWebpackEncoreEntries('app')` で `app` エントリーのアセットを読み込むよう指示

しているだけです。

これで、EasyAdminの管理画面上で郵便番号からの住所補完を実装することができました🙌

![](https://tva1.sinaimg.cn/large/008i3skNgy1gysayyfoi0g30wi09e43i.gif)
