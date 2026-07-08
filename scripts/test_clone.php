<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::whereIn('role', ['it', 'ketua_tim'])->first();
auth()->login($user);

$request = Illuminate\Http\Request::create('/api/file-manager?pokja=TKRS&type=ep&standar_id=1&item_id=1', 'GET');
try {
    $response = app()->handle($request);
    echo $response->getContent();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
