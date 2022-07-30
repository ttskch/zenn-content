---
title: "[Symfony] Twigのattribute関数で孫以下のプロパティにもアクセスしたい"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "twig"]
published: true
published_at: 2020-05-29
---

:::message
この記事は、2020-05-29に別のブログ媒体に投稿した記事のアーカイブです。
:::

# Twigのattribute関数

Twigでオブジェクトのプロパティにアクセスするには

```twig
{{ person.name }}
```

みたいにすればいいですよね。

では、プロパティ名が変数に格納されている場合はどうすればいいかと言うと、

```twig
{# propName == 'name' #}
{{ attribute(person, propName) }}
```

こんなふうに [attribute](https://twig.symfony.com/doc/3.x/functions/attribute.html) 関数を使えばOKです。

# 孫プロパティにもアクセスしたい

ではここで、以下のようなオブジェクトを考えてみましょう。

```php
class Person
{
    public $name;
    /** @var Address */
    public $address;
}
```

```php
class Address
{
    public $prefecture;
    public $city;
}
```

このとき、Twigで都道府県名を出力したければ、当然

```twig
{{ person.address.prefecture }}
```

とすればいいのですが、例えばちょっと変化球で、以下のように `person` 以下のプロパティパスを渡せば共通処理で値を出力してくれるような実装をしたいとしましょう。

```twig
名前: {% include 'widget.html.twig' with { propertyPath: 'name' } %}
都道府県: {% include 'widget.html.twig' with { propertyPath: 'address.prefecture' } %}
市区郡: {% include 'widget.html.twig' with { propertyPath: 'address.city' } %}
```

```twig
{# widget.html.twig #}

{{ attribute(person, propertyPath) }}
```

このとき、 `name` については

```twig
{{ attribute(person, 'name') }}
```

が実行されて正常に出力されますが、 `address.prefecture` `address.city` については

```twig
{{ attribute(person, 'address.prefecture') }}
{{ attribute(person, 'address.city') }}
```

を実行することになり、これは `attribute()` 関数の正しい使い方ではないのでエラーになってしまいます。

こんなふうに、 `attribute` 関数で孫以下のプロパティにもアクセスしたいというケースがたまにあります。

# 自分でTwig関数を作ってしまえば解決

こういう場合は、自分でTwig関数を実装してしまえば解決できます。

Symfonyの [PeropertyAccess](https://symfony.com/doc/current/components/property_access.html) コンポーネントを使えば、今回の例のようなドット区切りのプロパティパスで孫以下のプロパティにも簡単にアクセスできます。

例えば以下のような実装でよいでしょう。

```php
<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;

    public function __construct(PropertyAccessorInterface $accessor)
    {
        $this->accessor = $accessor;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getAttribute', [$this, 'getAttribute']),
        ];
    }

    public function getAttribute($objectOrArray, $propertyPath)
    {
        return $this->accessor->getValue($objectOrArray, $propertyPath);
    }
}
```

```yaml
# config/services.yaml

services:
    App\Twig\AppExtension:
        tags: ['twig.extension']
```

これで、Twig内で `getAttribute()` という関数が使えるようになるので、以下のようなコードが書けます👍

```twig
名前: {% include 'widget.html.twig' with { propertyPath: 'name' } %}
都道府県: {% include 'widget.html.twig' with { propertyPath: 'address.prefecture' } %}
市区郡: {% include 'widget.html.twig' with { propertyPath: 'address.city' } %}
```

```twig
{# widget.html.twig #}

{{ getAttribute(person, propertyPath) }}
```

> 参考： <https://stackoverflow.com/questions/23364538/access-child-entity-property-from-string-twig-symfony>

# まとめ

* Twigの `attribute()` 関数で孫以下のプロパティにもアクセスしたい場合、 `attribute()` 関数自体にそんな機能はないので、自分でTwig関数を実装してしまえばOK
