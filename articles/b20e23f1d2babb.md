---
title: "MacのChromeでアドレスバーでのCommand+Enterがテンキーで効かない問題の解決方法"
emoji: "🍎"
type: "idea"
topics: ["chrome", "mac"]
published: true
published_at: 2013-09-06
---

:::message
この記事は、2013-09-06に別のブログ媒体に投稿した記事のアーカイブです。
:::

Mac OS X の Google Chrome で、Cmd+Enter で検索結果を別タブで開くっていうのが、
テンキー上の Enter だと効かなくて困ってたけど、キーボードの印字をよくよく見たら
左は Return、右は Enter で違うキーだった。

KeyRemap4MacBook で Enter を Return にリマップしたら快適になりました。

```xml
<!-- private.xml -->
<?xml version="1.0"?>
<root>
    <item>
        <name>Enter to Return (private)</name>
        <identifier>enter_to_return</identifier>
        <appendix>Change Enter to Return</appendix>
        <autogen>__KeyToKey__ KeyCode::ENTER, KeyCode::RETURN</autogen>
    </item>
</root>
```
