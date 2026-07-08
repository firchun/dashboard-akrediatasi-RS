<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ep = App\Models\EpItem::first();
$request = Illuminate\Http\Request::create("/ep/{$ep->id}", 'PUT', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json',
], json_encode($ep->toArray()));

try {
    $response = app()->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() == 500) {
        $content = json_decode($response->getContent(), true);
        echo "Error: " . ($content['message'] ?? $response->getContent()) . "\n";
        echo "File: " . ($content['file'] ?? '') . ":" . ($content['line'] ?? '') . "\n";
    }
} catch (\Throwable $e) {
    echo "Caught: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}
