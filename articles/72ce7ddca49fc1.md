---
title: "[Mac便利アプリ] ウィンドウのリサイズや移動をキーボード操作でできるようにする「Rectangle」"
emoji: "🍎"
type: "idea"
topics: ["mac"]
published: true
published_at: 2020-04-06
---

:::message
この記事は、2020-04-06に別のブログ媒体に投稿した記事のアーカイブです。
:::

Macでウィンドウのリサイズや移動をキーボード操作でできるようにする便利アプリ「Rectangle」を紹介します。

# Rectangleとは

[Rectangle](https://github.com/rxhanson/Rectangle) は、ウィンドウのリサイズや移動をキーボード操作でできるようにするためのMacアプリです。

同種の [Spectacle](https://github.com/eczarny/spectacle) というアプリをベースに開発されています。Spectacleは [2019年10月でアクティブな開発が終了しており](https://github.com/eczarny/spectacle/commit/1dec1ca0923a1d96c3f621f72379354201ad2c66)、Rectangleが正式にその後継アプリとなっているようです。

僕も以前からSpectacleを愛用していたのですが、あるときふとサイトを見に行ったら `Important note: Spectacle is no longer being actively maintained` となっていて、慌ててRectangleを入れました。

# インストール方法

インストールはとても簡単です。

[公式サイト](https://rectangleapp.com/) からダウンロードするか、以下のように `brew cask` でもインストール可能です。

```bash
$ brew cask install rectangle
```

初回起動時にアクセシビリティの許可を求められます。

![](https://tva1.sinaimg.cn/large/00831rSTgy1gdjqt3biv5j30mc0t6dnp.jpg)

`システム環境設定を開きます` をクリックして、アクセシビリティの設定画面が開いたら、Rectangle.appにチェックをつけましょう。

![](https://tva1.sinaimg.cn/large/00831rSTgy1gdjqtzd0u0j30y60u0tot.jpg)

これでインストール完了です。

# 使い方

Rectangleが起動している間は、キーボード操作でアクティブなウィンドウのリサイズや移動が行えます。

デフォルトでは以下のような設定になっています。

![](https://tva1.sinaimg.cn/large/00831rSTgy1gdjqwngfa2j316g0u0gxs.jpg)

試しに `Option + ⌘ + →` をタイプしてみましょう。

![](https://tva1.sinaimg.cn/large/00831rSTgy1gdjrcwhxvgj31hc0u01kx.jpg)

これが…

![](https://tva1.sinaimg.cn/large/00831rSTgy1gdjrerum0rj31hc0u0qvk.jpg)

一瞬でこうなりました。便利！

# Spectacleとの比較

ちなみにSpectacleと設定項目のバリエーションを比較すると以下のような感じです。

| Spectacle | Rectangle |
| --- | --- |
| ![](https://tva1.sinaimg.cn/large/00831rSTgy1gdjrkmzm00j30xr0u0qkl.jpg) | ![](https://tva1.sinaimg.cn/large/00831rSTgy1gdjqwngfa2j316g0u0gxs.jpg) |

ほぼ同じような操作ができることが分かります。ほとんどの人が問題なく移行できそうですね。

# 僕の設定内容

ちなみに僕の場合は

* 左半分
* 右半分
* 上半分
* 下半分
* 最大化
* 小さくする
* 次のディスプレイ
* 前のディスプレイ

しか使わないので、もう少し押しやすい設定に変更してあります。（Spectacle時代からこの設定なので手が完全に慣れてしまって今さら変更に対応できません笑）

![](https://tva1.sinaimg.cn/large/00831rSTgy1gdjr60oebgj316g0u07gp.jpg)

ご参考まで。

# まとめ

* Macでウィンドウのリサイズや移動をキーボード操作でできるようにする「[Rectangle](https://github.com/rxhanson/Rectangle)」が便利
* Rectangleは、開発が終了した「[Spectacle](https://github.com/eczarny/spectacle)」の公式な代替アプリ
* Spectacleとほぼ同じ操作ができて問題なく移行可能
