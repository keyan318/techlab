<?php
// Faithful repro of POST /chat/message stream closure (no HTTP/cookie dance).
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ChatController;
use App\Services\NvidiaNimService;

session()->start();
Auth::loginUsingId(17);
echo "AUTH_CHECK=".(Auth::check()?'yes':'no')."\n";
echo "SESSION_STARTED=".(session()->isStarted()?'yes':'no')."\n";

$request = Request::create('/chat/message', 'POST', [
    'message' => 'A train travels 60 km in 45 minutes. What is its speed in km/h? Please explain step by step.',
    'conversation_id' => null,
    'level' => 'auto',
    'context' => '',
]);
$request->headers->set('Accept', 'text/plain');
// Bind the authenticated user to the request so $request->user() works (the
// web middleware does this in the real HTTP flow; console repro must fake it).
$request->setUserResolver(function () { return Auth::user(); });
$app->instance('request', $request);

$controller = new ChatController(app(NvidiaNimService::class));

// HYPOTHESIS 1 TEST: force session()->save() (line 212, OUTSIDE the try/catch)
// to throw by killing the DB connection the database session driver uses.
Illuminate\Support\Facades\DB::disconnect();

$response = $controller->send($request);
echo "RESPONSE_CLASS=".get_class($response)."\n";

// Capture streamed body; surface any exception thrown by the closure
// (in real HTTP this escapes $response->send() and becomes a PHP fatal -> NO monolog log).
ob_start();
$sendThrew = null;
try {
    $response->send();
} catch (\Throwable $e) {
    $sendThrew = get_class($e).': '.$e->getMessage();
}
$body = ob_get_clean();

echo "SEND_THREW=".($sendThrew ?? 'no')."\n";
echo "=== STREAM BODY (len=".strlen($body).") ===\n";
echo $body."\n";
echo "=== END BODY ===\n";
