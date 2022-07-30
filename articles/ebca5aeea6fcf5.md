---
title: "jsFiddleでconsole.logの結果を表示する＆npmライブラリを使う方法"
emoji: "💻"
type: "tech"
topics: ["javascript"]
published: true
published_at: 2020-07-05
---

:::message
この記事は、2020-07-05に別のブログ媒体に投稿した記事のアーカイブです。
:::

# jsFiddleとは

[jsFiddle](https://jsfiddle.net/) は、HTML/CSS/JavaScriptのコードをブラウザ上で実装・動作確認できるWebサービスです。ユーザー登録なしでも使えるので、簡単なコードの動作結果をちょっとシェアしたいときなんかにとても便利です。

# jsFiddleでconsole.logの結果を表示したい

<https://jsfiddle.net/ttskch/5r4bun1w/37/>

こちらをご覧ください。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ggg5h5w7b8j334m0u0af1.jpg)

`console.log（1）;` するだけのコードです。jsFiddleの出力画面には（当たり前ですが）ブラウザ領域には何も表示されておらず、コンソールに `1` が出力されていますね。

これでも一応動作結果を共有することはできますが、 `console.log()` の出力をブラウザ領域に表示するようにできると、より見やすくなりますよね。

> ちなみに、少し前にjsFiddleのアップデートがあってデフォルトでコンソールが表示されるようになりましたが、以前はデフォルトでは何も表示されていないブラウザ領域だけが見えていて、わざわざコンソールを表示しないと結果が見られませんでした。

というわけでその方法です。

とても簡単で、jsFiddleの左メニューの `Resources` に、以下のように `https://rawgit.com/eu81273/jsfiddle-console/master/console.js` というURLを入力して `+` をクリックするだけです。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ggg5n4g576j30bi0hadgx.jpg)

[eu81273/jsfiddle-console](https://github.com/eu81273/jsfiddle-console) が読み込まれて、 `console.log()` の出力をDOMに出力してくれるようになるというわけです。

これで、

<https://jsfiddle.net/ttskch/5r4bun1w/39/>

このように `console.log()` の出力内容がブラウザ領域に表示されます。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ggg5o008knj334p0u00xm.jpg)

> 参考：[jsfiddle - How do I display the results of console.log in JS Fiddle? - Web Applications Stack Exchange](https://webapps.stackexchange.com/questions/118819/how-do-i-display-the-results-of-console-log-in-js-fiddle/118821)

# jsFiddleでnpmのパッケージを使いたい

似たような話で、jsFiddleでnpmパッケージを使いたいなーということもあると思います。

これも同じように `Resources` で読み込めばよいです。npmパッケージをCDN化してくれるサービス [unpkg.com](https://unpkg.com/) を使います。

例えば、[lodash](https://lodash.com/docs/4.17.15) の `4.17.15` を使いたい場合なら、 `https://unpkg.com/lodash@4.17.15/lodash.js` というURLを `Resources` で読み込めばよいです。

<https://jsfiddle.net/ttskch/5r4bun1w/41/>

このように、jsFiddle内でlodashを使ったコードを実行できます。

![](https://tva1.sinaimg.cn/large/007S8ZIlgy1ggg5ye4qq4j334o0u0n2o.jpg)

> 参考：[javascript - How to import NPM package in JSFiddle? - Stack Overflow](https://stackoverflow.com/questions/46845199/how-to-import-npm-package-in-jsfiddle)
