<?php

$oldUrls = $newUrls = [];
$csv = new SplFileObject(__DIR__.'/map.csv', 'r');
while (!$csv->eof()) {
    $row = $csv->fgetcsv();
    if ($row[0] !== 'from' && $row[0] !== null) {
        $oldUrls[] = $row[0];
        $newUrls[] = $row[1];
    }
}

$articles = glob(__DIR__.'/../../articles/*.md');
$books = glob(__DIR__.'/../../books/*/*.md');
$files = array_merge($articles, $books);

foreach ($files as $file) {
    $content = file_get_contents($file);

    $replacements = [];

    // [text](https://blog.ttskch.com/permalink) -> [text](https://zenn.dev/ttskch/articles/slug)
    // [text](https://blog.ttskch.com/permalink/) -> [text](https://zenn.dev/ttskch/articles/slug)
    preg_match_all('%\[(.*?)\]\((https://blog\.ttskch\.com/.*?)(#.*?)?\)%', $content, $m);
    for ($i = 0; $i < count($m[0]); $i++) {
        $target = $m[2][$i];
        $oldUrl = preg_grep(sprintf('#%s#', $target), $oldUrls);
        $replacement = $newUrls[key($oldUrl)];
        $replacements[] = [$target, $replacement];
    }

    // [text](/permalink) -> [text](https://zenn.dev/ttskch/articles/slug)
    // [text](/permalink/) -> [text](https://zenn.dev/ttskch/articles/slug)
    preg_match_all('%\[(.*?)\]\((/.*?)(#.*?)?\)%', $content, $m);
    for ($i = 0; $i < count($m[0]); $i++) {
        $target = $m[2][$i];
        $oldUrl = preg_grep(sprintf('#%s#', $target), $oldUrls);
        $replacement = $newUrls[key($oldUrl)];
        $replacements[] = [$target, $replacement];
    }

    // https://blog.ttskch.com/permalink -> https://zenn.dev/ttskch/articles/slug
    // https://blog.ttskch.com/permalink/ -> https://zenn.dev/ttskch/articles/slug
    preg_match_all('%https://blog.ttskch.com/(.*?)(#.*?)?\)%', $content, $m);
    for ($i = 0; $i < count($m[0]); $i++) {
        $target = $m[1][$i];
        $oldUrl = preg_grep(sprintf('#%s#', $target), $oldUrls);
        $replacement = $newUrls[key($oldUrl)];
        $replacements[] = [$target, $replacement];
    }

    // [xxx | blog.ttskch] -> [xxx]
    preg_match_all('%\[(.*?) \| blog\.ttskch\]%', $content, $m);
    for ($i = 0; $i < count($m[0]); $i++) {
        $target = $m[0][$i];
        $replacement = sprintf('[%s]', $m[1][$i]);
        $replacements[] = [$target, $replacement];
    }

    foreach ($replacements as $replacement) {
        $content = str_replace($replacement[0], $replacement[1], $content);
    }

    file_put_contents($file, $content);
}
