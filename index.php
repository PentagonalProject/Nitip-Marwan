<?php
// -----------------------------------------------------
// include konfigurasi
// -----------------------------------------------------
require __DIR__ .'/config.php';

// -----------------------------------------------------
// include autoload composer
// -----------------------------------------------------
require __DIR__ .'/vendor/autoload.php';

// -----------------------------------------------------
// include functions
// -----------------------------------------------------
require_once __DIR__ . '/app/functions/helper.php';
require_once __DIR__ . '/app/functions/url.php';
require_once __DIR__ . '/app/functions/database.php';
require_once __DIR__ . '/app/functions/auth.php';

// -----------------------------------------------------
// Dispatch Router
// -----------------------------------------------------
/**
 * @var \FastRoute\Dispatcher
 */
$dispatcher = FastRoute\SimpleDispatcher(function (\FastRoute\RouteCollector $r) {
    $require = function () {
        require_once __DIR__ . '/app/router.php';
    };

    // binding ke \FastRoute\RouteCollector
    $require->call($r);
});

$uri = get_request_uri();
// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch(get_method(), $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header('Content-Type: text/html;charset=utf-8', 404);
        // ... 404 Not Found
        muat_layout('error/404', ['title' => '404 Halaman Tidak Ditemukan']);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        header('Content-Type: text/html;charset=utf-8', 405);
        // ... 405 Method Not Allowed
        muat_layout(
            'error/405',
            [
                'title' => '405 Method Not Allowed',
                'allowedMethods' => $routeInfo[1]
            ]
        );
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars    = $routeInfo[2];
        try {
            if (!is_callable($handler)) {
                throw new \RuntimeException(
                    "Router handler is not callable"
                );
            }
            call_user_func_array($handler, (array) $vars);
            // ... call $handler with $vars
        } catch (\Exception $exception) {
            header('Content-Type: text/html;charset=utf-8', 500);
            muat_layout(
                'error/500',
                [
                    'title' => '500 Internal Server Error',
                    'exception' => $exception
                ]
            );
        }
        break;
}
