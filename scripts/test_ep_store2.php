<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->get('config')->set('session.driver', 'array'); // bypass CSRF mostly, or just disable middleware
$app->instance(Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, new class {
    public function handle($request, $next) { return $next($request); }
});

$ep = App\Models\EpItem::with('uploadFiles')->first();
$request = Illuminate\Http\Request::create('/ep/' . $ep->id, 'PUT', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json',
], json_encode($ep->toArray()));

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
