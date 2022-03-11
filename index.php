<?php

use App\Redirect;
use App\Views\View;

require_once 'vendor/autoload.php';
session_start();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    //auth
    $r->addRoute('GET', '/register', ['App\Controllers\RegisterController', 'signup']);
    $r->addRoute('POST', '/register', ['App\Controllers\RegisterController', 'register']);
    $r->addRoute('GET', '/login', ['App\Controllers\LoginController', 'signin']);
    $r->addRoute('POST', '/login', ['App\Controllers\LoginController', 'login']);
    $r->addRoute('POST', '/logout', ['App\Controllers\LoginController', 'logout']);
    // homepage
    $r->addRoute('GET', '/homepage', ['App\Controllers\HomepageController', 'index']);
    // add listing
    $r->addRoute('GET', '/listings/create', ['App\Controllers\ListingsController', 'create']);
    $r->addRoute('POST', '/listings', ['App\Controllers\ListingsController', 'store']);
    //listings index page
    $r->addRoute('GET', '/listings', ['App\Controllers\ListingsController', 'index']);
    //show each listing
    $r->addRoute('GET', '/listings/{id}', ['App\Controllers\ListingsController', 'show']);
    //delete listing
    $r->addRoute('POST', '/listings/{id}/delete', ['App\Controllers\ListingsController', 'delete']);
    //edit
    $r->addRoute('POST', '/listings/{id}/update', ['App\Controllers\ListingsController', 'update']);
    $r->addRoute('GET', '/listings/{id}/edit', ['App\Controllers\ListingsController', 'edit']);
    //comment
    $r->addRoute('POST', '/listings/{id}/reviews', ['App\Controllers\ReviewsController', 'create']);
    //make reservation
    //$r->addRoute('GET', '/listings/{id}/reservations/create', ['App\Controllers\ReservationsController', 'create']);
    $r->addRoute('POST', '/listings/{id}/reservations', ['App\Controllers\ReservationsController', 'store']);


});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        var_dump('404 Not Found');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2] ?? [];

        $view = (new $handler)->$method($vars);

        $loader = new \Twig\Loader\FilesystemLoader('app/views');
        $twig = new \Twig\Environment($loader);

        if ($view instanceof View) {
            echo $twig->render($view->getPath(), $view->getVariables());
        }

        if ($view instanceof Redirect) {
            header('Location: ' . $view->getLocation());
            exit;
        }

        break;
}

if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}

if (isset($_SESSION['inputs'])) {
    unset($_SESSION['inputs']);
}