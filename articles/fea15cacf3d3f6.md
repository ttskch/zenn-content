---
title: "huskyを使ってマイグレーションスクリプトをコミットし忘れているときに自動で気づけるようにする"
emoji: "✍️"
type: "tech"
topics: ["husky", "symfony"]
published: true
---

メモです。

# はじめに

> PHPの [Symfony](https://symfony.com/) というフレームワークを使ったプロジェクトで体験した話ですが、他のフレームワークにも適用できる内容かと思います。

エンティティのスキーマを変更したのにマイグレーションスクリプトをコミットし忘れることがちょいちょいあります😓

テスト環境のDBはマイグレーションではなくエンティティの定義から直接スキーマが作られるので、マイグレーションスクリプトを作り忘れていてもテストは落ちず、気づかずにそのままマージしてしまってステージング環境が壊れる、ということが今までに何度かありました。

こりゃイカンということで自動で気付ける仕組みを導入してみたらいい感じになったので、シェアしておきます。

# やったこと

やったことはめっちゃ簡単で、**[husky](https://github.com/typicode/husky) を入れてshell scriptを3行書いただけです。**

具体的な手順は以下のとおりです。

```shell
$ yarn add -D husky
$ yarn husky install
$ yarn husky add .husky/pre-commit "todo"
```

とやると、`./.husky/pre-commit` として

```shell
#!/usr/bin/env sh
. "$(dirname -- "$0")/_/husky.sh"

todo
```

こんなファイルが作成されるので、

```diff
  #!/usr/bin/env sh
  . "$(dirname -- "$0")/_/husky.sh"
  
- todo
+ if [[ -n `git diff --name-only --cached src/Entity` ]] && [[ -z `git diff --name-only --cached migrations` ]]; then
+   echo "\033[30;43m\n\n[Warning] マイグレーションスクリプトをコミットし忘れていませんか？\n\n\033[m";
+ fi
```

こんな感じでスクリプトを書きます。

**「`src/Entity` 配下に `git add` 済みの変更があるにもかかわらず、`migrations` 配下に `git add` 済みの変更がない場合に、警告メッセージを表示する」** ということをやっているだけです。

これが [Git Hooks](https://git-scm.com/book/en/v2/Customizing-Git-Git-Hooks) の `pre-commit` フックで実行されるので、以下のようにコミット時に異変に気づくことができるというわけです。

![](https://img.esa.io/uploads/production/attachments/15064/2022/10/18/77821/4dba242f-bd6a-44bf-96b9-808da280c2f8.jpg)

> ちなみに、エンティティのファイルにスキーマと無関係な変更を加えることもあるので、あくまで警告文を表示するだけで、強制的にexitまではしないようにしています。

# husky使う必要ある？について

ところで、Git Hooksのフックのデフォルトの置き場所は `./.git/hooks` ですが、`git config core.hooksPath {任意のディレクトリ}` で設定を変更すれば自作のフックをコードベースに含めることが簡単にできるので、huskyを使わなくても実現は可能です。

特に今回はPHPプロジェクトだったのでhuskyのためだけに `package.json` を作ることになり、一瞬微妙かも？と思ったのですが、

* 開発者それぞれがローカルで `git config core.hooksPath foo/bar` を実行する
* 開発者それぞれがローカルで `yarn && yarn husky install` を実行する

の2択だったら後者のほうが構成として分かりやすいかなという感覚からhuskyを使いました。