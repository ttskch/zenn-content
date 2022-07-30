---
title: "[Symfony] DoctrineのCustom Mapping Typesを使って文字列の拡張型っぽいValueObjectを扱う"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "doctrine"]
published: true
published_at: 2021-10-13
---

:::message
この記事は、2021-10-13に別のブログ媒体に投稿した記事のアーカイブです。
:::

Symfonyで業務システムを作っていたら、`事業年度` と `四半期` というドメインモデルが出てきました。

例えば、「2021年度」という `事業年度` は

* `2021年度` という文字列表現
* `2021/4/1〜2022/3/31` という期間情報

を持ち、「2021年度第4四半期」という `四半期` は

* `2021年度第4四半期` という文字列表現
* `2022/1/1〜2022/3/31` という期間情報

を持つ、というような要件です。

このドメインモデルをコードに落とし込む際にやり方をいくつか検討したのですが、最終的に

* 期間情報を取り出すメソッドを持ったValueObjectとして表現し
* Doctrineの [Custom Mapping Types](https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/cookbook/custom-mapping-types.html) を使って文字列の拡張型っぽく保存する

というアプローチで割とスッキリと表現することができたので、その共有です✋

> `事業年度` も `四半期` も期間計算のロジックが多少違うだけでエッセンスは同じなので、以降は `四半期` モデルのみにフォーカスして解説していきます🙏

# 方針

1. DBには `2021年度第4四半期` といった文字列として永続化する
1. アプリ側ではこれが `Quarter` といったクラスのインスタンス（ValueObject）に変換されるようにする
1. `Quarter` クラスに `getStartedAt(): \DateTimeInterface` や `getEndedAt(): \DateTimeInterface` といったメソッドを生やして、期間情報を簡単に取り出せるようにする

というのが大方針です。これをDoctrineでどう実現するかというお話になります。

# 1. PHPで文字列の拡張型っぽいクラスを作る

まずは `Quarter` クラスを作ります。

`2021年度第4四半期` のような文字列表現と `2022/1/1〜2022/3/31` といった期間情報の2つを取り出せるクラスにしたいので、気持ちとしては `string` の拡張型っぽいクラスにしたいです。

もちろんPHPでは `string` はクラスではなくプリミティブ型なので拡張はできません。なので、

* コンストラクタ引数で文字列表現を受け取る

* `__toString()` を実装する
* 必要に応じて拡張メソッドを生やす

という方法で擬似的にこれを表現してみます。


https://twitter.com/ttskch/status/1447429294112739329

今回の例で言うと、`Quarter` クラスは具体的には以下のような内容になります。

```php
class Quarter
{
    private string $label;
    private \DateTimeInterface $startedAt;
    private \DateTimeInterface $endedAt;

    public function __construct(string $label)
    {
        if (!preg_match('/^(\d{4})年度第([1234])四半期$/', $label, $match)) {
            throw new \RuntimeException('四半期の文字列表現が正しくありません');
        }

        $this->label = $label;

        $y = (int) $match[1];
        switch ((int) $match[2]) {
            case 1:
                $m = 4;
                break;
            case 2:
                $m = 7;
                break;
            case 3:
                $m = 10;
                break;
            case 4:
            default:
                $m = 1;
                $y++;
                break;
        }
        $this->startedAt = new \DateTime(sprintf('%d-%d-1', $y, $m));
        $this->endedAt = (clone $this->startedAt)->add(new \DateInterval('P3M'))->sub(new \DateInterval('PT1S')); // 3ヶ月後の前日の23:59:59
    }

    public function __toString(): string
    {
        return $this->label;
    }

    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    public function getEndedAt(): \DateTimeInterface
    {
        return $this->endedAt;
    }
}
```

これで、`Quarter` クラスのインスタンスは、`2021年度第4四半期` のような文字列として扱うこともでき、なおかつ `getStartedAt()` `getEndedAt()` メソッドを用いて期間情報を取得することもできる便利オブジェクトになりました。

# 2. DoctrineのCustom Mapping Typesを使って透過的に変換する

あとは、DBに文字列として保存されている情報がDoctrineから取り出したときに自動で `Quarter` クラスに変換されるようになればOKです。

これは、Doctrineの [Custom Mapping Types](https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/cookbook/custom-mapping-types.html) という機能を使えば簡単に実現できます。

まず、以下のような感じで、`string` DBAL Typeを拡張した `quarter` DBAL Typeを自作します。

```php
namespace App\Doctrine\DBAL\Types;

use App\Model\Quarter;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class QuarterType extends StringType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new Quarter($value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName()
    {
        return 'quarter';
    }
}
```

このとき、`requiresSQLCommentHint()` を上書きして `return true;` するようにしないと、`bin/console doctrine:migrations:diff` で何度やっても差分が出るという現象になるので要注意です。（[参考](https://github.com/doctrine/dbal/issues/2596#issuecomment-429793257)）

あとは、`doctrine.yaml` でこのCustom Mapping Typesを登録してあげればOKです。（[公式ドキュメント](https://symfony.com/doc/current/doctrine/dbal.html#registering-custom-mapping-types)）

```yaml
# config/packages/doctrine.yaml

doctrine:
    dbal:
        types:
            quarter: App\Doctrine\DBAL\Types\QuarterType
```

これで、Doctrine ORMで `quarter` DBAL Typeを使えるようになったので、エンティティに `Quarter` 型のプロパティを作って、以下のような感じでアノテートすることができます。

```php
/**
 * @ORM\Column(type="quarter", length=255, nullable=true)
 */
public ?Quarter $quarter = null;
```

これで、

* DBには文字列として保存される
* アプリ側では `Quarter` クラスのインスタンスとして取得される

という振る舞いが実現できました🙌

# まとめ

* `事業年度` や `四半期` といった、「基本的には単なる文字列でしかなくていいけど、簡単な変換処理をそれ自身に持たせたい」ようなドメインモデルが出てきた
* DoctrineのCustom Mapping Typesを使って「文字列の拡張型」っぽいValueObjectdをマッピングしてあげたらスッキリ表現できてよかった

何かの参考になれば幸いです💡
