---
title: "[Symfony + Sentry] エラー発生時のログインユーザーIDもSentryに送る"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "sentry"]
published: true
---

Sentryにエラーを報告する際に、そのときログインしていたユーザーのIDをあわせて送る方法について解説します。

> PHPの [Symfonyフレームワーク](https://symfony.com/) にSentryを統合している場合のコード例で説明します。他のフレームワーク・言語をお使いの場合は適宜読み替えてください🙏

Symfonyの場合はこちらのドキュメントにやり方が書いてありました。

[Scrubbing Sensitive Data for Symfony | Sentry Documentation](https://docs.sentry.io/platforms/php/guides/symfony/data-management/sensitive-data/)

# 1. イベントを加工するコールバックを実装

```php
// src/Sentry/BeforeSend.php

namespace App\Sentry;

use App\Entity\User;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\UserDataBag;
use Symfony\Bundle\SecurityBundle\Security;

class BeforeSend
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(Event $event, EventHint $eventHint): Event
    {
        if (($user = $this->security->getUser()) instanceof User) {
            $event->setUser(new UserDataBag($user->getId()));
        }

        return $event;
    }
}
```

# 2. 設定ファイルで `before_send` オプションに上記コールバックを指定

```yaml
# config/packages/sentry.yaml

when@prod:
  sentry:
    options:
      before_send: App\Sentry\BeforeSend
```

# 3. おわり

これだけです。

この状態で実際にエラーを発生させてみると、下図のようにユーザーIDがSentryに記録されます🙆‍♂️

![](https://img.esa.io/uploads/production/attachments/15064/2023/05/23/77821/8d81e89a-3709-4d34-805d-3e1aeec8d2c3.png)
