---
title: "PHPUnitでprivateメソッドをテストする"
emoji: "🐘"
type: "tech"
topics: ["php", "phpunit"]
published: true
published_at: 2020-06-06
---

:::message
この記事は、2020-06-06に別のブログ媒体に投稿した記事のアーカイブです。
:::

テストが必要なほどの責務をprivateメソッドに持たせるのはどうなんだといった話は置いておいて、いざというときのためにやり方を知っていることには意味があると思うので記事にしておきます😇

> ちなみに、「privateメソッドは実装の詳細であり外から見た振る舞いではないので個別にテストする必要はない。publicメソッド経由でテストすればいい。」というご意見もあるようです。
>
> **プライベートメソッドのテストは書かないもの？ - t-wadaのブログ**
> <https://t-wada.hatenablog.jp/entry/should-we-test-private-methods>

# やり方

やり方はあっけないぐらい簡単で、[ReflectionClass](https://www.php.net/manual/ja/class.reflectionclass.php) を使えばシュッと実現できます。

**privateメソッドの実行**

以下のようにして `ReflectionClass` 経由でprivateメソッドを実行することができます。

```php
$reflection = new \ReflectionClass($object);
$method = $reflection->getMethod('メソッド名');
$method->setAccessible(true);
$result = $method->invoke($object, '引数1', '引数2' /* 可変長引数 */);
```

**privateプロパティの読み取り**

ちなみに同様に `ReflectionClass` 経由でprivateプロパティを読むこともできます。

```php
$reflection = new \ReflectionClass($object);
$property = $reflection->getProperty('プロパティ名');
$property->setAccessible(true);
$value = $property->getValue($object);
```

# テストコードで使う場合

以下のような感じでテストクラスのprivateメソッドにまとめると使いやすいかもしれません。

```php
use Foo;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FooTest extends TestCase
{
    public function testPrivateMethod()
    {
        $foo = new Foo();
        $result = $this->invokePrivateMethod($foo, 'somePrivateMethod', ['param1', 'param2']);
        $this->assertEquals('expected result', $result);
    }

    public function testPrivateProperty()
    {
        $foo = new Foo();
        $foo->doSomething();
        $value = $this->readPrivateProperty($foo, 'somePrivateProperty');
        $this->assertEquals('expected value', $value);
    }

    private function invokePrivateMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invoke($object, ...$parameters);
    }

    private function readPrivateProperty($object, string $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
```

# 参考リンク

* <https://qiita.com/nao_tuboyaki/items/eb4bab18339c63f27ee8>
