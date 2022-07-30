---
title: "[Symfony][Doctrine] STIのdiscriminatorを指定してWHERE句を書きたい"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "doctrine"]
published: true
published_at: 2020-07-23
---

:::message
この記事は、2020-07-23に別のブログ媒体に投稿した記事のアーカイブです。
:::

# やりたいこと

* Doctrineの [Single Table Inheritance](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/inheritance-mapping.html#single-table-inheritance) を使っているエンティティがある
* このエンティティを所有する別のエンティティのRepositoryにおいて、discriminator（識別子）の値をWHERE句から参照したい

> Single Table Inheritanceについては [こちらの過去記事](https://blog.ttskch.com/symfony-doctrine-single-table-inheritance/) でも紹介しているので参照してみてください✋

# 普通にやろうとすると…

[こちらの過去記事](https://blog.ttskch.com/symfony-doctrine-single-table-inheritance/) と同じ、

* `労働者` というベースクラスを継承した `会社員` エンティティと `フリーランス` エンティティがある
* `案件` エンティティが `労働者` エンティティをOneToManyで所有している

というケースを例にコードを見てみたいと思います。

まずはダメなパターンです🙅‍♂️

```php
$filteredWorkers = $this->createQueryBuilder('m')
    ->leftJoin('m.workers', 'w')
    ->andWhere('w.type = :employee')
    ->andWhere('w.salary > :salary')
    ->setParameter('employee', 'employee')
    ->setParameter('salary', 400000)
    ->getQuery()
    ->getResult()
;
```

一見行けそうに見えますが、実行すると以下のようなエラーになりクエリの組み立てが失敗します。

```
[Semantical Error] line 0, col xx near 'type = ':employee'': Error: Class App\Entity\Worker has no field or association named type
```

# 正解1：派生エンティティごとにリレーションシップを張っておく

過去記事の [#種類ごとにManyToOne](https://blog.ttskch.com/symfony-doctrine-single-table-inheritance/#種類ごとにmanytoone) のパートで紹介した方法で、あらかじめ `Matter` エンティティに `workers` だけでなく `employees` と `freelancers` も持たせておきましょう。

そうすれば、以下のように普通にJOINすることができます👍

```php
$filteredWorkers = $this->createQueryBuilder('m')
    ->leftJoin('m.employees', 'e')
    ->andWhere('e.salary > :salary')
    ->setParameter('salary', 400000)
    ->getQuery()
    ->getResult()
;
```

# 正解2：DQLの `INSTANCE OF` を使う

何かの事情で派生エンティティとのリレーションシップがない場合には、[DQLの `INSTANCE OF`](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/dql-doctrine-query-language.html#7c4bfc024554885cccaa6ee529acc353d8e01f48:~:text=Get%20all%20instances%20of%20a%20specific%20type%2C%20for%20use%20with%20inheritance%20hierarchies%3A) を使ってWHERE句の中で絞り込むこともできます👍

```php
$filteredWorkers = $this->createQueryBuilder('m')
    ->leftJoin('m.workers', 'w')
    ->andWhere('w INSTANCE OF :employeeClass')
    ->andWhere('w.salary > :salary')
    ->setParameter('employeeClass', Employee::class)
    ->setParameter('salary', 400000)
    ->getQuery()
    ->getResult()
;
```

# まとめ

* Doctrineで、Single Table Inheritanceを使っているエンティティのdiscriminator（識別子）を指定してWHERE句を書きたい場合は、以下のどちらかの方法で解決できる
    * [派生エンティティごとにリレーションシップを張っておいて](https://blog.ttskch.com/symfony-doctrine-single-table-inheritance/#種類ごとにManyToOne)、派生エンティティとJOINする
    * [DQLの `INSTANCE OF`](https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/dql-doctrine-query-language.html#7c4bfc024554885cccaa6ee529acc353d8e01f48:~:text=Get%20all%20instances%20of%20a%20specific%20type%2C%20for%20use%20with%20inheritance%20hierarchies%3A) を使ってWHERE句の中で絞り込む
