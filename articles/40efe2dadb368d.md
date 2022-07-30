---
title: "Symfony2のSpBowerBundleでなぜかパッケージがコンフリクトするときの対処"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony"]
published: true
published_at: 2015-01-06
---

:::message
この記事は、2015-01-06に別のブログ媒体に投稿した記事のアーカイブです。
:::

Symfony2 の [SpBowerBundle](https://github.com/Spea/SpBowerBundle) で bower を使っていて

```
$ php app/console sp:bower:install

   :
   :

bower                        ECONFLICT Unable to find suitable version for angular
                                                     
  [Sp\BowerBundle\Bower\Exception\RuntimeException]  
  An error occured while installing dependencies     
```

こんなふうにコンフリクトしてインストールできないとき。

`bower.json` を見る限りバージョンの指定はおかしくないし、何より自分の環境でしかエラーが起きてない、みたいなときは、既存の `components` ディレクトリを丸ごと削除して再度インストールし直すと行けることが多いです。

```
$ rm -rf $(find src -regex ".*\/Resources\/public\/components$")
$ php app/console sp:bower:install
```
