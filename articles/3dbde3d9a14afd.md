---
title: "[Symfony/Validation] Callback制約の使い方"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "validation"]
published: true
published_at: 2020-07-25
---

:::message
この記事は、2020-07-25に別のブログ媒体に投稿した記事のアーカイブです。
:::

# 普通のバリデーション

Symfonyにおいて、フォーム経由で作成されたエンティティをバリデーションする方法はいくつかありますが、最も一般的なのはエンティティのプロパティにアノテーションで書く方法だと思います。

```php
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    /**
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
    
    /**
     * @Assert\NotBlank()
     */
    private $password;
}
```

# 他のプロパティの値を使ったバリデーションをしたいとき

では、例えば **「 `name` や `email` の文字列に含まれるような文字列は `password` にはセットできない」** というバリデーションを書きたければどうすればいいでしょうか？

## 1. カスタムバリデーションを作る

[symfony/formで「どちらか片方の入力は必須、かつ両方入力はNG」をバリデーションする | blog.ttskch](https://blog.ttskch.com/symfony-form-exclusive-or-validation/)

こちらの過去記事でも紹介したようにカスタムバリデーションを作ればもちろん実装できます。

## 2. Callback制約を使う

が、もう少し手軽な方法として [Callback制約](https://symfony.com/doc/current/reference/constraints/Callback.html) というのが用意されているので、今回はこれを使っていみたいと思います。

`Callback` 制約は、他の制約と違って **プロパティではなくメソッドにアノテートします。**

それにより、そのメソッドがバリデーション実行時にコールされるようになるというものです。

```php
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    /**
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
    
    /**
     * @Assert\NotBlank()
     */
    private $password;
    
    /**
     * @Assert\Callback()
     */
    public function validatePassword(ExecutionContextInterface $context)
    {
        if (strpos($this->name, $this->password) !== false || strpos($this->email, $this->password) !== false) {
            $context->buildViolation('名前やメールアドレスに含まれる文字列はパスワードに設定できません')->atPath('password')->addViolation();
        }
    }
}
```

こんな感じです。便利ですね🙌
