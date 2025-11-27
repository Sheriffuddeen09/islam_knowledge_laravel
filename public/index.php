<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $request = Illuminate\Http\Request::capture();

    $response = $kernel->handle($request);

    $response->send();

    $kernel->terminate($request, $response);
} catch (\PDOException $e) {
    // MySQL server down
    if ($e->getCode() === 2002) {
        http_response_code(500);
        echo json_encode(['message' => 'Server down, please try later']);
        exit;
    }
    throw $e;
} catch (\Throwable $e) {
    // fallback for all other exceptions
    http_response_code(500);
    echo json_encode(['message' => 'Something went wrong']);
    exit;
}
