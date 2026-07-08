<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ep = App\Models\EpItem::with('uploadFiles')->first();

$request = Illuminate\Http\Request::create('/ep/' . $ep->id, 'PUT', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
], json_encode($ep->toArray()));

$controller = app()->make(\App\Http\Controllers\EpItemController::class);
try {
    $response = $controller->update($request, $ep->id);
    echo "Success!\n";
} catch (\Throwable $e) {
    echo "Error on line " . $e->getLine() . ": " . $e->getMessage() . "\n";
}
