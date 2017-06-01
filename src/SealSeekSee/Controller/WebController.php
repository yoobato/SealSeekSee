<?php
namespace SealSeekSee\Controller;

use SealSeekSee\Util\What3WordsUtil;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class WebController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        /**
         * @var $web \Silex\ControllerCollection
         */
        $web = $app['controllers_factory'];

        $web->get('/', array($this, 'index'));

        return $web;
    }

    public function index(Request $req, Application $app)
    {
        // TODO: 테스트 코드
        $coordinates = What3WordsUtil::address2Coordinates('index.home.raft');
        $address = What3WordsUtil::coordinates2Address(51.521251,-0.203586);

        return $app['twig']->render('/index.twig', array('coordinates' => $coordinates, 'address' => $address));
    }
}
