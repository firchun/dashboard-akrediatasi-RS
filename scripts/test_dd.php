<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/ep/1', 'PUT', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
], json_encode(['bukti_r' => true]));

try {
    $request->boolean('bukti_r');
    echo "boolean() worked!\n";
} catch (\Throwable $e) {
    echo "Error on boolean: " . $e->getMessage() . "\n";
}
