---
title: "[Symfony] Doctrine ORMでarray型にオブジェクトの配列を保存しているときに正常に更新ができない問題の解決方法"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "doctrine"]
published: true
published_at: 2020-12-11
---

:::message
この記事は、2020-12-11に別のブログ媒体に投稿した記事のアーカイブです。
:::

[Symfony Advent Calendar 2020](https://qiita.com/advent-calendar/2020/symfony) の11日目の記事です！🎄🌙

昨日も僕の記事で、[[Symfony] 機能テストでコントローラに注入しているサービスをモックする方法](https://zenn.dev/ttskch/articles/ab2973d60ead0a) でした✨

> ちなみに、僕はよく [TwitterにもSymfonyネタを呟いている](https://twitter.com/search?q=from%3Attskch%20(symfony%20OR%20doctrine)&src=typed_query&f=live) ので、よろしければぜひ [フォローしてやってください🕊🤲](https://twitter.com/ttskch)

この記事では、Doctrine ORMでarray型にオブジェクトの配列を保存しているときに、正常に更新ができない問題の解決方法について説明します。

# どういうこと

例えば、以下のような `Company` エンティティを考えます。

```php
// src/Entity/Company.php
/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 */
class Company
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var array|Address[]
     *
     * @ORM\Column(type="array")
     */
    public array $addresses = [];
}
```

```php
// src/ValueObject/Address.php
class 
{
    public ?string $zipCode = null;
    public ?string $prefectuire = null;
    public ?string $city = null;
    public ?string $line1 = null;
    public ?string $line2 = null;
}
```

このように、住所を表す `Address` というValueObjectがあり、 `Company` エンティティがその配列を持つとします。

> オブジェクトの配列をシリアライズしてDBに保存するのは典型的なアンチパターンだと思いますが、ここではその是非については議論しません🙏

このとき、編集画面のコントローラアクションを以下のように「普通に」実装すると、**実は正常に更新ができません。**

```php
public function company_edit(Request $request, Company $company)
{
    $form = $this->createForm(CompanyType::class, $company);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->em->flush();
        return $this->redirectToRoute('company_show', ['id' => $company->getId()]);
    }

    return [
        'company' => $company,
        'form' => $form->createView(),
    ];
}
```

説明しづらいですが、見た目としては、もともと保存されていた `Company#addresses` の要素が意図しない内容で更新されたり、更新が適用されなかったりといった現象が発生します。

# 原因と解決方法

これは、Doctrine ORMがエンティティの変更を検知する際に、**「同じオブジェクトで中身だけが変わった」ときには「変更なし」と判断してしまう** ために起こる現象のようです。

なので、この例では以下のように一旦 `$company->addresses` の中身を1つ1つcloneして **オブジェクトそのものが変わったよ** という状態にしてあげれば、 `$company->addresses` 全体が期待どおり更新されてくれます👍

```diff
  public function company_edit(Request $request, Company $company)
  {
      $form = $this->createForm(CompanyType::class, $company);
      $form->handleRequest($request);
  
      if ($form->isSubmitted() && $form->isValid()) {
+         $company->addresses = array_map(fn($v) => clone $v, (array) $company->addresses);
          $this->em->flush();
          return $this->redirectToRoute('company_show', ['id' => $company->getId()]);
      }
  
      return [
          'company' => $company,
          'form' => $form->createView(),
      ];
  }
```

> 参考：[php - How to force Doctrine to update array type fields? - Stack Overflow](https://stackoverflow.com/questions/11084209/how-to-force-doctrine-to-update-array-type-fields/13231876#answer-13231876)

覚えておくといつか役に立つかもです。

以上です！

[Symfony Advent Calendar 2020](https://qiita.com/advent-calendar/2020/symfony)、明日は [@ippey_s](https://twitter.com/ippey_s) さんです！お楽しみに！
