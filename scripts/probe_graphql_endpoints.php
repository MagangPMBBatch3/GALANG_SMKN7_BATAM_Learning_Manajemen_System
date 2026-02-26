<?php
$hosts = [
    'http://127.0.0.1:8000',
    'http://127.0.0.1',
    'http://localhost:8000',
    'http://localhost',
];
$paths = [
    '/graphql',
    '/public/index.php/graphql',
    '/index.php/graphql',
    '/maxcourse/public/index.php/graphql',
    '/maxcourse/graphql',
];

$query = '{ systemStats { total_users } }';

foreach ($hosts as $host) {
    foreach ($paths as $path) {
        $url = rtrim($host, '/') . $path;
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode(['query' => $query]),
                'timeout' => 5,
            ],
        ];
        $ctx = stream_context_create($opts);
        echo "Testing: $url\n";
        $start = microtime(true);
        $result = @file_get_contents($url, false, $ctx);
        $dur = round((microtime(true) - $start) * 1000);
        if ($result === false) {
            $err = isset($http_response_header) ? implode(" | ", $http_response_header) : 'no response';
            echo "  FAILED ($dur ms) - $err\n\n";
            continue;
        }
        // try decode
        $json = json_decode($result, true);
        if ($json === null) {
            echo "  OK ($dur ms) - response not JSON (length=" . strlen($result) . ")\n";
            echo "  Preview: " . substr(trim($result), 0, 200) . "\n\n";
            continue;
        }
        echo "  OK ($dur ms) - JSON response keys: " . implode(',', array_keys($json)) . "\n";
        echo "  Preview: " . substr(json_encode($json), 0, 200) . "\n\n";
    }
}

echo "Done.\n";
