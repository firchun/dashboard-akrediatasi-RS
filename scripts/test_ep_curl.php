<?php
// Let's boot the app and simulate a JSON request
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ep = App\Models\EpItem::with('uploadFiles')->first();

$request = Illuminate\Http\Request::create('/ep/' . $ep->id, 'PUT', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json',
], json_encode($ep->toArray()));

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
