---
title: "Symfony+TwigでTailwind CSS"
emoji: "🎻"
type: "tech"
topics: ["php", "symfony", "tailwindcss"]
published: true
published_at: 2024-12-06 07:00
---

[Symfony Advent Calendar 2024](https://qiita.com/advent-calendar/2024/symfony) の6日目の記事です！🎄✨

> Twitter (X) でもちょいちょいSymfonyネタを呟いてます。よろしければ [フォロー](https://x.com/ttskch) お願いします🤲

# `symfonycasts/tailwind-bundle` を使えばOK

あとで付随的なことを色々書きますが、とりあず結論としては [symfonycasts/tailwind-bundle](https://symfony.com/bundles/TailwindBundle/current/index.html) を使えばOKです。

ただし、このバンドルは [Asset Mapper](https://symfony.com/doc/current/frontend/asset_mapper.html) との併用が前提なので、[Webpack Encore](https://symfony.com/doc/current/frontend/encore/index.html) を使っているという方は、[このドキュメント](https://tailwindcss.com/docs/guides/symfony) を参照するか **[Asset Mapperに乗り換えてください](https://symfony.com/blog/upgrading-symfony-websites-to-assetmapper)**。

```shell
$ composer require symfonycasts/tailwind-bundle
$ bin/console tailwind:init
```

これで、プロジェクトルートに `tailwind.config.js` が作成され、`assets/styles/app.css` にお馴染みの [`@tailwind` ディレクティブ](https://tailwindcss.com/docs/functions-and-directives) が書き込まれます。

```css:assets/styles/app.css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

この、Tailwind CSS用の入力CSSファイルを、直接Twigで読み込んでしまえばOKです。

```twig
{# templates/base.html.twig #}

{% block stylesheets %}
    <link rel="stylesheet" href="{{ asset('styles/app.css') }}">
{% endblock %}
```

こう書いておけば、Tailwind CSSでコンパイルした後のCSSファイル（`var/tailwind/app.built.css` に出力されます）をバンドルが自動で読み込んでくれます。便利ー！

CSSを書いたら

```shell
$ bin/console tailwind:build
```

というコマンドでTailwind CSSのコンパイルを実行できます。

開発中は

```shell
bin/console tailwind:build --watch
```

を起動させておくと楽です。

Symfony Local Web Serverの [worker](https://symfony.com/doc/current/setup/symfony_server.html#configuring-workers) を使って、`.symfony.local.yaml` に

```yaml:.symfony.local.yaml
workers:
    tailwind:
        cmd: [symfony, console, tailwind:build, --watch]
```

のように書いておけば、`symfony serve` している間 裏で勝手に `symfony console tailwind:build --watch` が起動してくれるのでもっと楽です。

本番用にビルドするときは

```shell
$ bin/console tailwind:build --minify
$ bin/console asset-map:compile
```

って感じにすれば、`public/assets/` 配下にTailwind CSSコンパイル後のCSSが適切に配置されます。便利ー！

Functional Testの実行にもコンパイル後のCSSが必要となるので要注意です。

```json:composer.json
{
    "scripts": {
        "test": [
            "bin/console tailwind:build #",
            "@php -c php.ini -d memory_limit=-1 vendor/bin/phpunit"
        ]
    }
}
```

# PhpStormでTailwind CSSの補完が効くようにする

https://x.com/ttskch/status/1853878314987106792

https://x.com/ttskch/status/1854019714537910391

https://x.com/ttskch/status/1854019716396003382

上記のとおり時間を溶かしまくったのですが、[PhpStormのTailwind CSSプラグイン](https://www.jetbrains.com/help/phpstorm/tailwind-css.html)（デフォルトで組み込まれていて有効化されている）は `node_modules` にTailwind CSSがインストールされていることを前提として動作するようなので、TailwindBundle経由でTailwind CSSを利用しているプロジェクトでは補完が効きません。

解決策は簡単で、補完を効かせるためだけに `npm i -D tailwindcss` して、**その上でTailwind CSSプラグインを一度無効化し、再度有効化する** ことで補完が効くようになります。

# Twigファイル内のクラス属性の内容をPrettierでソートする

実際にコードを書き始めてみると、クラス属性が自動でソートされない中でTailwind CSSを使うことがどれほどストレスフルかを痛感します。

痛感した結果、以下のPrettierプラグインを作りました。Twig + Tailwind CSSをやるなら導入必須だと思います。

https://zenn.dev/ttskch/articles/db73d0703f93dc

以上です。

[Symfony Advent Calendar 2024](https://qiita.com/advent-calendar/2024/symfony)、明日は空きです🥺どなたかぜひご参加ください！
