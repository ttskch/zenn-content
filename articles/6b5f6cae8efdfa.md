---
title: "[Mac] togglのデスクトップアプリを入れたらすべてが捗って最高だった件"
emoji: "🍎"
type: "idea"
topics: ["mac"]
published: true
published_at: 2020-05-10
---

:::message
この記事は、2020-05-10に別のブログ媒体に投稿した記事のアーカイブです。
:::

# togglとは

[toggl](https://toggl.com/) はシンプルなタイムトラッキングツールです。

作業の開始時と終了時にタイマーをスタート/ストップすることで、「何にどれくらい時間を使ったか」を記録しておくことができます。

普通に個人で使う分には [完全に無料で利用できます](https://www.toggl.com/feature-list/)👍

僕は仕事の時間計測のために使っています。

# Alfred Workflow

[こちらの過去記事](https://zenn.dev/ttskch/articles/45f27d84969a15) で軽く触れましたが、togglのタイマーをAlfredからスタート/ストップするために [こちらのWorkflow](http://www.packal.org/workflow/alfred-time-v2) を使っています。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1gelakap4bwj30uw060tdt.jpg)

こんな感じでスタートして、

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1gelal9sf5kj30uw08gn5g.jpg)

こんな感じでストップできます。

これでも十分便利に使っていたんですが…

# Mac用デスクトップアプリを入れたら世界が変わった

Mac用の [デスクトップアプリ](https://www.toggl.com/toggl-desktop/) を入れたら世界が変わりました😂

> `brew cask install toggl` でもインストールできます。

インストールするとメニューバーに常駐して、Webサイトに行かなくてもここから色々な操作ができます。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1gelaud9iywj30aw0kcgut.jpg)

が、これはもともとAlfred Workflowで達成できていました。

それよりもライフチェンジングだったのは以下の3つの機能です。

## 1. Macの画面をスリープさせると自動でタイマーがストップする！

これマジ革命的でした。

基本的に席を立つときは [Ctrl + Cmd + Q](https://support.apple.com/ja-jp/HT201236#sleep) で画面をスリープする習慣なので、タイマーの止め忘れが完全になくなりました🙌

## 2. タイマーをスタートせずにしばらく作業してると、「タイマースタート忘れてない？」と通知が出る！

タイマーをスタートせずにしばらくPCを触っていると、以下のような通知が出ます。タイマーのスタートし忘れもなくなりました🙌

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1geld8xdutnj30il03e74s.jpg)	

## 3. 今の作業にどれぐらいかかっているのかが見える！

これは前の2つに比べると革命度は低いですが、地味にかなり嬉しいやつでした。

タイマー稼働中は

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1gelaragfmlj30nw018t94.jpg)

こんな感じで今どれぐらい時間が経っているかが常にメニューバーに表示されているので、「ヤバ、この作業にもう2時間も使っちゃってるわ。急がな。」とかが自然と意識できて効率が上がります🙌

# まとめ

* Macでtoggl使ってるけどデスクトップアプリ入れてないという人は今すぐ入れるべき！
