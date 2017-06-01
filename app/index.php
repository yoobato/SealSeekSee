<?php
$autoloader = require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$autoloader->add('SealSeekSee', '..' . DIRECTORY_SEPARATOR . 'src');

$app = new Silex\Application();

require 'conf.php';

// Twig
$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => __DIR__ . DIRECTORY_SEPARATOR . 'views',
    )
);

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->mount('/', new SealSeekSee\Controller\WebController());

if ($app['debug']) {
    \Symfony\Component\Debug\ErrorHandler::register();
    \Symfony\Component\Debug\ExceptionHandler::register();
} else {
    $app->error(function (Exception $e, $code) use ($app) {
        if ($code == \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND) {
            return $app->redirect('/');
        }
    });
}

$app->run();
