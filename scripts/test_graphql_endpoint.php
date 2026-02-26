<?php
$url = 'http://127.0.0.1:8000/graphql';
$rawQuery = $argv[1] ?? '{ __schema { queryType { name } } }';
$data = ['query' => $rawQuery];
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true,
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === false) {
    echo "Request failed\n";
    var_dump($http_response_header ?? null);
    exit(1);
}
echo $result . PHP_EOL;
