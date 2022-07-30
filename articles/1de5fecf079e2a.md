---
title: "phpenv（php-build）でapcuなどのPHP拡張をインストールする方法"
emoji: "🐘"
type: "tech"
topics: ["php", "phpenv", "mac"]
published: true
published_at: 2020-06-12
---

:::message
この記事は、2020-06-12に別のブログ媒体に投稿した記事のアーカイブです。
:::

PECLで別途インストールするのではなくPHPインストール時に同時に任意のPHP拡張をインストールする方法です。

phpenv環境自体の作り方は以下の過去記事をご参照ください。

> [[Mac] phpenv＋nodebrewでローカル開発環境を作る手順（保存版）](https://zenn.dev/ttskch/articles/2d05e5e3fd6083)

# 結論：環境変数で渡す

`phpenv install` コマンド実行時に `PHP_BUILD_INSTALL_EXTENSION` 環境変数を使ってインストールしたいPHP拡張を指定します。

例えばPHP 7.4.13と一緒に [apcu](https://pecl.php.net/package/APCu) の最新版をインストールしたければ

```bash
PHP_BUILD_INSTALL_EXTENSION="apcu=@" phpenv install 7.4.13
```

のようにすればよいです。

ただし、このように `=@` で最新版をインストールできるのは、以下の定義ファイルにビルド方法が指定されている拡張のみです。

[~/.phpenv/plugins/php-build/share/php-build/extension/definition](https://github.com/php-build/php-build/blob/master/share/php-build/extension/definition)

定義ファイルにないものをインストールしたい場合は、バージョン番号を明示する必要があります。

```bash
PHP_BUILD_INSTALL_EXTENSION="pdo_pgsql=1.0.2" phpenv install 7.4.13
```

> 定義ファイルに自分で行を追記してビルド方法を指定してあげれば、 `=@` でインストールすることもできますが、面倒でしょう。

複数の拡張をインストールしたい場合は、以下のようにスペース区切りで並べてあげればOKです。

```bash
PHP_BUILD_INSTALL_EXTENSION="apcu=@ imagick=@" phpenv install 7.4.13
```

> ただ、以下の過去記事でも紹介しているとおり、最近のphpenvだと `PHP_BUILD_CONFIGURE_OPTS` 環境変数で大量のコンパイルオプションを指定しないと実際にはビルドできないので要注意です。
> 
> > [[Mac] phpenv＋nodebrewでローカル開発環境を作る手順（保存版）](https://zenn.dev/ttskch/articles/2d05e5e3fd6083)

# デフォルトでxdebugがインストールされているのはなぜ？

PHPのビルド処理の流れを設定しているファイルが

```
~/.phpenv/plugins/php-build/share/php-build/definitions/{PHPのバージョン番号}
```

にあります。

例えば `7.4.13` のファイルを見てみると、中身はこんな感じになっています。

```
configure_option "--enable-gd"
configure_option "--with-jpeg"
configure_option "--with-zip"

install_package "https://secure.php.net/distributions/php-7.4.13.tar.bz2"
install_xdebug "3.0.1"
enable_builtin_opcache
```

この `install_xdebug "3.0.1"` によってxdebugがインストールされるわけですが、具体的にはphp-buildの以下のコードが実行される感じです。

<https://github.com/php-build/php-build/blob/5781f7defee700434e7b2fe19d179d6007d6c9a1/share/php-build/plugins.d/xdebug.sh#L12-L14>  
<https://github.com/php-build/php-build/blob/7b025743f93b4ee06c46102419324a61716ca7ca/share/php-build/extension/extension.sh#L9-L46>

なので、

```
~/.phpenv/plugins/php-build/share/php-build/definitions/{PHPのバージョン番号}
```

に

```
install_extension "apcu" "5.1.19"
```

のように追記した上で `phpenv install` を実行しても、PHP拡張がインストールされるはずです。（試してません）

ちなみに先ほどの `PHP_BUILD_INSTALL_EXTENSION` 環境変数を指定した場合の動作は

<https://github.com/php-build/php-build/blob/05737c4e4d2c7f45debb03734182b12a240fc686/bin/php-build#L903>  
<https://github.com/php-build/php-build/blob/05737c4e4d2c7f45debb03734182b12a240fc686/bin/php-build#L446-L462>

こんなふうになっています✋

# 参考記事

* [phpenv/php-build で拡張がすこし入れやすくなりました - Qiita](https://qiita.com/kunit/items/d2db65f81d57cad96b52)
* [php-buildを単独で使う - Qiita](https://qiita.com/Hiraku/items/33372d2c60b3ceb26e52)
* [php-build をカスタマイズして使う - y_uti のブログ](https://y-uti.hatenablog.jp/entry/2015/12/11/091058)
