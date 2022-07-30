<?php

$files = glob(__DIR__.'/../../../blog.ttskch.com/blog/_posts/*.md');

$map = [];
$skipped = [];
$fixme = [];

foreach ($files as $file) {
    $content = file_get_contents($file);

    // frontmatterと本文をそれぞれ取得
    preg_match("/^(---\s*\n.*?\n---)\s*\n*(.+)$/s", $content, $m);
    if (count($m) !== 3) {
        echo sprintf('Parse error: %s', $file);
        exit;
    }
    $frontmatter = $m[1];
    $body = $m[2];

    // frontmatterから必要な情報を取り出す
    preg_match("/\ntitle:\s*(.*?)\s*\n/s", $frontmatter, $m);
    $title = $m[1];
    $title = trim($title, '"\'');
    $title = str_replace("''", "'", $title);
    $title = str_replace('""', '"', $title);
    $title = str_replace('"', '\"', $title);
    preg_match("/\npermalink:\s*(.*?)\s*\n/s", $frontmatter, $m);
    $permalink = $m[1];
    $permalink = trim($permalink, '"\'');
    $permalink = trim($permalink, '/');
    preg_match("/\ndate:\s*(.*?)\s*\n/s", $frontmatter, $m);
    $date = $m[1];
    $date = trim($date, '"\'');
    preg_match("/\ntags:\s*(.*?)\n\S/s", $frontmatter, $m);
    $tags = $m[1];
    $tags = array_map(fn($v) => trim($v), array_filter(explode('-', $tags)));
    $tags = array_map(fn($v) => trim($v, '\'"'), $tags);
    $tags = array_map(fn($v) => strtolower($v), $tags);
    $tags = array_map(fn($v) => preg_replace('/[\s.\-_]/', '', $v), $tags);

    // 技術記事以外はスキップ
    if (in_array('暮らし', $tags, true)) {
        $skipped[] = [$file, $permalink, $title];
        continue;
    }
    if (array_values($tags) === ['雑記']) {
        $skipped[] = [$file, $permalink, $title];
        continue;
    }

    // Zenn本になっている記事はスキップ
    if (preg_match('/Angular実践入門チュートリアル/', $title)) {
        $skipped[] = [$file, $permalink, $title];
        continue;
    }
    if (preg_match('/実務でSymfonyアプリを作るときにだいたい共通してやっていること/', $title) && $permalink !== 'symfony-realworld-example-app') {
        $skipped[] = [$file, $permalink, $title];
        continue;
    }

    // タイトルが70文字超のものは手動で調整が必要
    if (mb_strlen($title) > 70) {
        $fixme[] = [$file, $permalink, $title];
        continue;
    }

    // タグの内容に応じてemojiを設定
    $emoji = '📝';
    if (in_array('symfony', $tags, true)) {
        $emoji = '🎻';
    } elseif (in_array('php', $tags, true)) {
        $emoji = '🐘';
    } elseif (in_array('ruby', $tags, true)) {
        $emoji = '💎';
    } elseif (in_array('github', $tags, true)) {
        $emoji = '🐙';
    } elseif (in_array('docker', $tags, true)) {
        $emoji = '🐳';
    } elseif (in_array('firebase', $tags, true)) {
        $emoji = '🔥';
    } elseif (in_array('mac', $tags, true) || in_array('iphone', $tags, true)) {
        $emoji = '🍎';
    } elseif (in_array('プログラミング', $tags, true)) {
        $emoji = '💻';
    }

    // タグの内容に応じてtypeを設定
    $type = 'tech';
    if (in_array('ビジネス', $tags, true)) {
        $type = 'idea';
    } elseif (in_array('iphone', $tags, true)) {
        $type = 'idea';
    } elseif (in_array('it', $tags, true) && !in_array('プログラミング', $tags, true)) {
        $type = 'idea';
    }

    // Zennに移行するにあたり冗長になるタグを削除
    $tags = array_filter($tags, fn ($v) => $v !== 'プログラミング');
    $tags = array_filter($tags, fn ($v) => $v !== 'it');

    $topics = sprintf('[%s]', implode(', ', array_map(fn ($v) => sprintf('"%s"', $v), $tags)));

    $template = <<<EOS
---
title: "%s"
emoji: "%s"
type: "%s"
topics: %s
published: true
published_at: %s
---
EOS;
    $frontmatter = sprintf($template, $title, $emoji, $type, $topics, $date);

    // 本文を整形
    $body = preg_replace("/(^|\n)##/s", '$1#', $body); // 見出しのレベルを1つずつ上げる
    $body = preg_replace("/(^|\n):::\s*v-pre\s*\n+/s", '$1', $body); // ::: v-pre の行を削除
    $body = preg_replace("/(^|\n):::\s*$/s", '$1', $body); // ::: v-pre を閉じる ::: の行を削除
    $body = preg_replace("/(^|\n):::\s*tip\s+(\S+)\n(.+):::\n+/s", "$1:::message\n**$2**\n$3:::\n\n", $body); // ::: tip を :::message に変換

    // ツイートの埋め込みを単なるURLに変換
    $body = preg_replace("#\n+<blockquote.*?(https://twitter.com/\S+/status/[^\"?]+).*?</script>#s", "\n\n$1", $body);

    // 自分のツイートのURLがユーザー名変更前のものだったら置換
    $body = str_replace('https://twitter.com/qckanemoto', 'https://twitter.com/ttskch', $body);

    // 本文冒頭に移行記事である旨を追記
    $body = sprintf(":::message\nこの記事は、%sに別のブログ媒体に投稿した記事のアーカイブです。\n:::\n\n%s", $date, $body);

    // 記事ファイルを作成
    $content = sprintf("%s\n\n%s\n", trim($frontmatter), trim($body));
    $slug = substr(md5(uniqid()), 0, 14);
    file_put_contents(__DIR__.sprintf('/../../articles/%s.md', $slug), $content);

    // 旧URLと新URLの対応を記憶
    $map[] = [
        sprintf('https://blog.ttskch.com/%s/', $permalink),
        sprintf('https://zenn.dev/ttskch/articles/%s', $slug),
    ];
}

$csv = new SplFileObject(__DIR__.'/map.csv', 'w');
$csv->fputcsv(['from', 'to']);
foreach ($map as $row) {
    $csv->fputcsv($row);
}

$csv = new SplFileObject(__DIR__.'/skipped.csv', 'w');
$csv->fputcsv(['file', 'permalink', 'title']);
foreach ($skipped as $row) {
    $csv->fputcsv($row);
}

$csv = new SplFileObject(__DIR__.'/fixme.csv', 'w');
$csv->fputcsv(['file', 'permalink', 'title']);
foreach ($fixme as $row) {
    $csv->fputcsv($row);
}
