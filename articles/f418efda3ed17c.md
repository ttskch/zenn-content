---
title: "[Symfony] エンティティのプロパティがオブジェクトの場合は @Assert\\Valid が必要なので注意"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony"]
published: true
published_at: 2020-05-31
---

:::message
この記事は、2020-05-31に別のブログ媒体に投稿した記事のアーカイブです。
:::

# どういうこと

例えば、以下のようなエンティティがあるとします。

```php
class Foo
{
    /**
     * @var Bar
     */
    private $bar;
    
    /**
     * @Assert\NotBlank()
     */
    private $baz;
    
    // ...
}
```

```php
class Bar
{
    /**
     * @Assert\NotBlank()
     */
    private $qux;
    
    /**
     * @Assert\Regex("/^\d+$/")
     */
    private $quux;
    
    // ...
}
```

これらのクラスに対応するFormTypeも以下のように用意してみます。

```php
class FooType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bar', BarType::class)
            ->add('baz', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Foo::class,
        ]);
    }
}
```

```php
class BarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('qux', TextType::class)
            ->add('quux', NumberType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bar::class,
        ]);
    }
}
```

これで、 `FooType` のフォームを使えば自動的に以下のプロパティがすべてバリデーションしてもらえそうな気がしますね。

* `Foo::baz`
* `Bar::qux`
* `Bar::quux`

しかし、実はこのままだと

* `Foo::baz`

しかバリデーションされません🙄

`Bar::qux` が空でも、 `Bar::quux` に数字以外が入っていても、特にエラーにならずにsubmitできてしまいます。

# どうすればいいの

プロパティにオブジェクトを持ち、そのオブジェクトのプロパティにもバリデーションが設定されている場合、 **親のプロパティに `Valid` 制約をつける必要があります。**

こちらの公式ドキュメントにすべて書いてあります👍  
<https://symfony.com/doc/current/reference/constraints/Valid.html>

先ほどの例で言うと、 `Foo` クラスを以下のように修正することで解決します。

```diff
class Foo
{
    /**
     * @var Bar
+    *
+    * @Assert\Valid()
     */
    private $bar;
    
    /**
     * @Assert\NotBlank()
     */
    private $baz;
    
    // ...
}
```

深くネストしている場合はすべての階層で `Valid` アノテートする必要があるので、付け忘れに注意ですね。

オブジェクトのプロパティに `Valid` アノテートする代わりに、FormTypeで直接

```php
->add('bar', BarType::class, [
    'constraints' => new Valid(),
])
```

としてもよいです👌

この方法なら階層が深くても最下層に1回書くだけでいいので少し楽かもしれません✋

# まとめ

* Symfonyでエンティティのプロパティがオブジェクトの場合はプロパティに `@Assert\Valid` しておかないとオブジェクト内部のバリデーションが適用されないので要注意
* アノテーションの代わりにFormTypeの当該フィールドに `'constraints' => new Valid()` を書くでもOK
