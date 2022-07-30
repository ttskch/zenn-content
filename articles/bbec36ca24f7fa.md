---
title: "Docker ToolboxからDocker for Macへの移行手順"
emoji: "🐳"
type: "tech"
topics: ["docker"]
published: true
published_at: 2016-06-22
---

:::message
この記事は、2016-06-22に別のブログ媒体に投稿した記事のアーカイブです。
:::

Docker Toolboxの環境は残しつつDocker for Macを使い始めることもできますが、あえて完全移行したい場合は以下のような手順でDocker Toolboxを削除すると良さそうです。

# 1. Docker for Macをインストール

<https://docs.docker.com/docker-for-mac/>
ここからインストーラーをダウンロードして、 `Docker.app` をインストール。

インストール後、 `Docker.app` を起動すると、「Docker Toolbox用のdefaultマシンが見つかったけど、コピーします？」的な案内が出るので、コピーする。
結構時間がかかる。

完了したらもうDocker for Mac使える。簡単！

# 2. .bashrc（など）からDocker Toolbox用の環境変数の設定を削除

```
eval $(docker-machine env default)
```

こういうのが .bashrc とかに仕込んであるはずなので、もう必要ないので削除する。

これを削除した状態でシェルを立ち上げて、 `docker` コマンドが使えたらDocker for Macが動作していることが確認できる。

もし `docker` コマンドが command not found になる場合は、 `Docker.app` を再起動すれば多分直る。

# 3. Docker Toolboxをアンインストール

`$ docker images` してみて問題なさそうだったらもうDocker Toolboxは要らないので削除する。

[Mac OSXでDocker Toolboxのアンインストール](http://qiita.com/minamijoyo/items/ec5b35382797ac08e067)
こちらの記事にあるとおり、公式のアンインストーラースクリプトを実行すればOK。

# 4. VirtualBoxからDocker Toolbox用のマシン（defaultなど）を削除

VirtualBoxにDocker Toolbox用のマシンがあるはずなので、これも削除する。
