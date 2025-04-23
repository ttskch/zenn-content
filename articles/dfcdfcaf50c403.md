---
title: "[Symfony][Doctrine] OneToOneのエンティティの削除が上手くいかなかったけど解決した"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "doctrine"]
published: true
---

# 起きたこと

- `User` エンティティが `Profile` エンティティをOneToOneで所持している
- `User::$profile` はnullableだが、`Profile::$user` はnon-nullable（DBレイヤーで）

という構成のエンティティがありました。（例であり、実際とは異なります）

```php:src/Entity/User.php
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\OneToOne(inversedBy: 'user', cascade: ['remove'])]
    private ?Profile $profile = null;
    
    // ...
}
```

```php:src/Entity/Profile.php
#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\OneToOne(mappedBy: 'profile')]
    private ?User $user = null;
    
    // ...
}
```

`Profile::$user` はnon-nullable（親たるユーザーなしにプロフィールは単体で存在できない）なので、ユーザーを削除すれば自動的にプロフィールも削除されるよう、`User::$profile` には `cascade: ['remove']` を付けています。（この場合なら `orphanRemoval: true` でも代替できます）

一方、`User::$profile` はnullable（子たるプロフィールを持たないユーザーは存在できる）なので、プロフィールを削除したからと言って自動的にユーザーまで削除されてしまっては困ります。なので `Profile::$user` には `cascade: ['remove']` は付けていません。

この状態でプロフィール単体の削除を試みると、`ForeignKeyConstraintViolationException` が発生しました。

![](https://img.esa.io/uploads/production/attachments/15064/2025/04/24/77821/9450e442-5826-47c2-8e96-8da0dc0eb3bf.png)

> [EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html) 経由で削除を試みた際のエラー画面なので、EasyAdminによってラップされたエラーメッセージになっています🙏

これは当然の結果で、`Profile::$user` には `cascade: ['remove']` や `orphanRemoval: true` を付けていないので、プロフィールを削除しようとしても依然としてユーザーが紐づいたままであり、外部キー制約によって削除できません。

このような場合、EntityListenerなどを使って、削除する前に親ユーザーとの紐づけを自動で断ち切るようにするのがセオリーです。

```php:src/EntityListener/ProfileListener.php
#[AsEntityListener(entity: Profile::class)]
final readonly class ProfileListener
{
    public function preRemove(Profile $profile, PreRemoveEventArgs $event): void
    {
        $profile->setUser(null);
    }
}
```

この状態でプロフィール単体の削除を試みると、なるほど今度は正常に成功しました👍

しかし、この状態で **ユーザーごと削除** を試みると、**また `ForeignKeyConstraintViolationException` が発生してしまいました。**

![](https://img.esa.io/uploads/production/attachments/15064/2025/04/24/77821/50025d66-efdc-4900-bd88-5e05f602decf.png)

# 原因

この現象は、以下のようなカラクリによって起こっています。

1. ユーザーを削除しようとする
1. `cascade: ['remove']` によってプロフィールの削除処理（[DoctrineのUnit of Work](https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/working-with-objects.html) （UoW）への削除依頼登録）が走る
1. `ProfileListener::preRemove()` によって親ユーザーとの関係が断ち切られる
1. 続けて、ユーザーの削除処理が走る
1. プロフィールとユーザーの削除がUoWに依頼されている状態で、UoWによって実際のクエリの発行処理が始まる
1. 親子関係のある（外部キー制約によって削除順が規定される）複数のエンティティを削除しようとする場合、UoW内の [`computeDeleteExecutionOrder()`](https://github.com/doctrine/orm/blob/3.3.2/src/UnitOfWork.php#L1145) というメソッドによって適切な削除順が計算されるのだが、**`ProfileListener::preRemove()` によって親子関係が断ち切られているため、削除順の計算が適切に行われず、プロフィール→ユーザーの順にDELETEクエリが発行されてしまう**
1. プロフィールのDELETEクエリを発行した時点で、外部キー制約違反が発生する

それが証拠に、`ProfileListener::preRemove()` の処理をコメントアウトした状態なら、ユーザーごと削除は正常に成功します。この場合は、`computeDeleteExecutionOrder()` によって削除順が正しく計算され、ユーザー→プロフィールの順にDELETEクエリが発行されるようになるからです。

> ちなみに、プロフィール単体の削除の場合には、UoWの [`computeChangeSets()`](https://github.com/doctrine/orm/blob/3.3.2/src/UnitOfWork.php#L350) というメソッドによって「プロフィールを削除する前にuserテーブルのprofile_idをnullに更新する必要がある」ということが計算されて、プロフィールのDELETEクエリの前にユーザーのUPDATEクエリが発行されるという挙動になります。よくできてますね〜。

# 対処

プロフィール単体の削除をできるようにするため、`ProfileListener::preRemove()` は残しておく必要があります。

**`cascade: ['remove']`（や `orphanRemoval: true`）経由でユーザーごと削除しようとしたときのみ `ProfileListener::preRemove()` の処理が走らないように** できれば解決するわけですが、`ProfileListener::preRemove()` の時点ではまだユーザーの削除はUoWに予約されていないため、残念ながら `ProfileListener::preRemove()` 内で「今自分は単体で削除されようとしているのか、それともユーザーごと削除されようとしているのか」を知る術はありません。

というわけで、結論としては **`cascade: ['remove']`（や `orphanRemoval: true`）を使うのをやめるしかありません。**

そして、`UserListener::preRemove()` を書いて自前でプロフィールの削除依頼を行うようにします。

```diff:src/Entity/User.php
  #[ORM\Entity(repositoryClass: UserRepository::class)]
  class User
  {
-     #[ORM\OneToOne(inversedBy: 'user', cascade: ['remove'])]
+     #[ORM\OneToOne(inversedBy: 'user')]
      private ?Profile $profile = null;
      
      // ...
  }
```

```php:src/EntityListener/UserListener.php
#[AsEntityListener(entity: User::class)]
final readonly class UserListener
{
    public function preRemove(User $user, PreRemoveEventArgs $event): void
    {
        if ($user->getProfile() !== null) {
            $event->getObjectManager()->getUnitOfWork()->scheduleForDelete($user->getProfile());
        }
    }
}
```

ユーザーごと削除するときに `ProfileListener::preRemove()` を実行させないことが目的なので、

```php
$event->getObjectManager()->remove($user->getProfile());
```

ではなく

```php
$event->getObjectManager()->getUnitOfWork()->scheduleForDelete($user->getProfile());
```

という低レベルなAPIを使って削除依頼を行う必要があります。

おわり。
