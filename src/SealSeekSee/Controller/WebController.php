<?php
namespace SealSeekSee\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

class WebController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        /** @var $web \Silex\ControllerCollection */
        $web = $app['controllers_factory'];

        $web->get('/', function () use ($app) {
            return $app['twig']->render('/base.twig');
        });

        return $web;
    }
}
