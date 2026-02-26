<?php

$query = <<<'GQL'
query {
    users(first:5, page:1) {
        data {
            id
            name
            email
            created_at
            roles { name }
        }
        paginatorInfo { currentPage total }
    }
}
GQL;

$payload = json_encode(['query' => $query]);

$opts = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $payload,
        'timeout' => 10,
    ],
];

$context = stream_context_create($opts);

$urls = [
    'http://127.0.0.1/maxcourse/public/index.php/graphql',
    'http://localhost/maxcourse/public/index.php/graphql',
    'http://127.0.0.1/maxcourse/public/graphql',
    'http://localhost/maxcourse/public/graphql',
    'http://127.0.0.1/maxcourse/graphql',
    'http://localhost/maxcourse/graphql',
    'http://127.0.0.1/graphql',
    'http://localhost/graphql',
];

$res = false;
foreach ($urls as $u) {
    $res = @file_get_contents($u, false, $context);
    if ($res !== false) {
        echo "URL: $u\n";
        break;
    }
}
if ($res === false) {
    $err = error_get_last();
    echo "Request failed: " . ($err['message'] ?? 'unknown') . PHP_EOL;
    exit(1);
}

echo $res . PHP_EOL;
