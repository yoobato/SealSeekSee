<?php
namespace SealSeekSee\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

class WebController implements ControllerProviderInterface
{
    // https://sealseeksee.yoobato.com/letter/find
    const LETTER_CHECK_SHORTEN_URL = 'http://bit.ly/2sJjJXD';

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
