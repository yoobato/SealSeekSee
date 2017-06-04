<?php
namespace SealSeekSee\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

class ApiController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        /**
         * @var $api \Silex\ControllerCollection
         */
        $api = $app['controllers_factory'];

        return $api;
    }
}
