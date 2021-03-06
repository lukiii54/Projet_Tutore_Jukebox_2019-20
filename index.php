<?php
require_once 'vendor/autoload.php';
require_once 'src/config/config.inc.php';

use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

$container = array();

$container["view"] = function ($container){

    $view = new \Slim\Views\Twig(__DIR__.'/src/views');
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    return $view;
};

$container['settings'] = $config;

//Eloquent
$app = new \Slim\App($container,[
    'settings' => [
        'debug' => true,
        'displayErrorDetails' => true
    ]
]);

/**
 * on initialise la conn
 */
$capsule = new DB();
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};


$app->get('/', function(Request $request, Response $response, $args){
    return $this->view->render($response, 'accueil.html.twig');
})->setName('accueil');;

$app->get('/createPlaylist', "\\app\\controllers\\controller:createPlaylist")->setName('createPlaylist');
$app->get('/checkPlaylist', "\\app\\controllers\\controller:checkPlaylist")->setName('checkPlaylist');
$app->get('/checkJukebox', "\\app\\controllers\\jukeboxController:showJukebox")->setName('checkJukebox');
$app->get('/checkReclam', "\\app\\controllers\\controller:checkReclam")->setName('checkReclam');


try {
    $app->run();
} catch (Throwable $e) {
    var_dump($e);
}