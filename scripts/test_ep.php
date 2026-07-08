<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ep = App\Models\EpItem::first();
echo "Testing EP update on ID: " . $ep->id . "\n";
try {
    $ep->update(['nilai' => 'TL']);
    echo "Update successful.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
