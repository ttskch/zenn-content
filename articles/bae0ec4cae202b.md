---
title: "[Mac便利アプリ] 一時的に自動スリープを止める「Caffeine」"
emoji: "🍎"
type: "idea"
topics: ["mac"]
published: true
published_at: 2020-02-26
---

:::message
この記事は、2020-02-26に別のブログ媒体に投稿した記事のアーカイブです。
:::

こんにちは、たつきちです。

エンジニア歴12年ぐらいで今はベンチャー企業のCTOをしています。

この記事では、macOS用の便利なユーティリティアプリ「Caffeine」をご紹介します。

会議などで自分のMacBookの画面をスクリーンに映しながら話し込んでいたら、スクリーンセーバーが立ち上がってしまって、慌てて解除したらログインパスワードを入力しないといけなくなって、なんだかワチャワチャしちゃうことってありませんか？（僕はあります）

Caffeineをインストールしておけばこういうのをスマートに回避できるので、とてもオススメです👍

ぜひ最後までお付き合いください。

# Caffeine（カフェイン）とは

[Caffeine](https://www.macupdate.com/app/mac/24120/caffeine) は、 **Macの自動スリープやスクリーンセーバーを一時的に作動させなくする** ための便利なユーティリティアプリです。

インストールするとメニューバーに以下のようなコーヒーカップのアイコンが常駐するようになり、これをクリックするだけで自動スリープ・スクリーンセーバーを一時的に止めることができます。

![](https://user-images.githubusercontent.com/4360663/75126352-e57ad080-56fc-11ea-953c-0b0446c85003.png)

# インストール方法

Homebrewで以下のようにしてインストールするか、[MacUpdate](https://www.macupdate.com/app/mac/24120/caffeine) などのサイトからダウンロードすることができます。

```bash
$ brew cask install caffeine
```

> Homebrew自体のインストール方法や使い方については [公式サイト](https://brew.sh/index_ja) をご参照ください。

# 使い方

インストールして起動すると、メニューバーに以下のようなコーヒーカップのアイコンが常駐します。

![](https://user-images.githubusercontent.com/4360663/75126352-e57ad080-56fc-11ea-953c-0b0446c85003.png)

アイコンをクリックすると以下のようにカップにコーヒーが入った状態に変わります。

この状態が「CafffeineがONの状態」つまり、「自動スリープ・スクリーンセーバーを止めている状態」です。

![](https://user-images.githubusercontent.com/4360663/75126448-56ba8380-56fd-11ea-809b-4d4c25167de3.png)

`右クリック → Activate for →` で、指定した時間だけONの状態にさせておくこともできます。（選んだ時間が経過したら自動でOFFの状態に戻ります）

![](https://user-images.githubusercontent.com/4360663/75126895-5e7b2780-56ff-11ea-8b61-767100793d5d.png)

> `Indefinitely` は無限（時間制限なし）という意味ですね。

# 設定変更

`右クリック → Preferences...` で設定を変更できます。

![](https://user-images.githubusercontent.com/4360663/75126488-85385e80-56fd-11ea-8b5a-21699e5c94a1.png)

![](https://user-images.githubusercontent.com/4360663/75126529-b4e76680-56fd-11ea-9fd5-3aeb42ce3d4e.png)

* `Automatically start Caffeine at login`
    * ログイン後にアプリを自動起動するかどうか
* `Activate Caffeine at launch`
    * 起動時に自動でアクティブ（ONの状態）にするかどうか
* `Show this message when starting Caffeine`
    * この画面自体を起動時に毎回表示するかどうか
* `Default duration`
    * 「自動スリープ・スクリーンセーバーを止めておく時間」をデフォルトでどの長さにするか

が設定できます。

OFFにし忘れることを特別懸念しないなら、 `Default duration` は `Indefinitely` （無限）にしておくのがいいかと思います。

# まとめ

* Caffeineを入れておけば、会議などで自分のMacBookの画面をスクリーンに映しながら話し込んでいたらスクリーンセーバーが立ち上がってしまうといった問題を簡単に回避できて便利！
