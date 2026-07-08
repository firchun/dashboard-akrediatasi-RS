<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ep = App\Models\EpItem::first();
$data = $ep->toArray();
$data['upload_files'] = [['file' => 'test.jpg']];

$request = Illuminate\Http\Request::create("/ep/{$ep->id}", 'PUT', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json',
], json_encode($data));

$controller = app()->make(\App\Http\Controllers\EpItemController::class);
try {
    $response = $controller->update($request, $ep->id);
    echo "Status: " . $response->getStatusCode() . "\n";
} catch (\Throwable $e) {
    echo "Caught: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}
