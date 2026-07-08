<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ep = App\Models\EpItem::first();
$request = Illuminate\Http\Request::create('/ep/' . $ep->id, 'PUT', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
], json_encode(['bukti_r' => true]));

try {
    $x = $request->has('bukti_r') ? $request->boolean('bukti_r') : $ep->bukti_r;
    echo "It worked: " . ($x ? 'true' : 'false') . "\n";
} catch (\Throwable $e) {
    echo "Error on line: " . $e->getMessage() . "\n";
}
