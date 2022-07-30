---
title: "[Symfony] 現在のパスワードも確認のために入力させるパスワード変更フォームの作り方"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony"]
published: true
published_at: 2020-04-21
---

:::message
この記事は、2020-04-21に別のブログ媒体に投稿した記事のアーカイブです。
:::

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ge1k12sixhj30sm0dgwfg.jpg)

こういうフォームの作り方です。

# 結論

[UserPassword](https://symfony.com/doc/current/reference/constraints/UserPassword.html) Constraintsを使えば簡単に作れます👍

# 具体的なコード例

**FormType**

```php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UserChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => '現在のパスワード',
                'constraints' => [
                    new Assert\NotBlank(),
                    new SecurityAssert\UserPassword([
                        'message' => '現在のパスワードが正しくありません',
                    ]),
                ],
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => '新しいパスワード',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
        ;
    }
}
```

こんな感じで、「現在のパスワード」のほうに `UserPassword` Constraintsをセットします。

これだけで、ログイン中のユーザーのパスワードが正しく入力されたかどうかをチェックしてくれます。便利〜！

**コントローラ**

あとはコントローラ側で普通にフォームから受け取った「新しいパスワード」の文字列をエンコードして保存すればOKです。

```php
public function changePasswordAction(Request $request, UserPasswordEncoderInterface $encoder)
{
    $user = $this->getUser();

    $form = $this->createForm(UserChangePasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user->setPassword($encoder->encodePassword($user, $form->get('newPassword')->getData()));

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', 'パスワードの変更が完了しました。');

        return $this->redirectToRoute('xxx');
    }

    return [
        'user' => $user,
        'form' => $form->createView(),
    ];
}
```

こんな感じですかね。

# まとめ

* Symfonyで「現在のパスワード」と「新しいパスワード」を入力させてユーザーのパスワードを変更するフォームを作るときは、[UserPassword](https://symfony.com/doc/current/reference/constraints/UserPassword.html) Constraintsを使えばすごく簡単に作れる
