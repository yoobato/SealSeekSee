<?php
namespace SealSeekSee\Controller;

use SealSeekSee\Util\What3WordsUtil;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        /**
         * @var $web \Silex\ControllerCollection
         */
        $web = $app['controllers_factory'];

        $web->get('/', function() use ($app) {
            return $app['twig']->render('/index.twig');
        });

        $web->get('/letter/write', array($this, 'renderWriteLetterPage'));

        $web->get('/letter/check', array($this, 'renderCheckLetterPage'));
        $web->match('/letter/check/map', array($this, 'renderCheckLetterMapPage'));
        $web->match('/letter/read', array($this, 'renderReadLetterPage'));

        return $web;
    }

    public function renderWriteLetterPage(Request $req, Application $app)
    {
        $sender_phone = $req->get('sender_phone', '');

        return $app['twig']->render('write_letter.twig',
            array(
                'sender_phone' => $sender_phone
            )
        );
    }

    public function renderCheckLetterPage(Request $req, Application $app)
    {
        $receiver_phone = $req->get('receiver_phone', '');

        return $app['twig']->render('check_letter.twig',
            array(
                'receiver_phone' => $receiver_phone
            )
        );
    }

    public function renderCheckLetterMapPage(Request $req, Application $app)
    {
        $receiver_phone = $req->get('receiver_phone', '');
        if (empty($receiver_phone)) {
            return new Response('잘못된 휴대전화번호');
        }

        $word1 = $req->get('word1', '');
        $word2 = $req->get('word2', '');
        $word3 = $req->get('word3', '');
        if (empty($word1) || empty($word2) || empty($word3)) {
            return new Response('잘못된 단어', Response::HTTP_BAD_REQUEST);
        }

        $w3w_address = $word1 . '.' . $word2 . '.' . $word3;
        $latLng = What3WordsUtil::address2Coordinates($w3w_address);

        // TODO: letter 가지고 와서 보여주자

        return $app['twig']->render('check_letter_map.twig',
            array(
                'letter_lat' => $latLng['lat'],
                'letter_lng' => $latLng['lng']
            )
        );
    }

    public function renderReadLetterPage(Request $req, Application $app)
    {
        // TODO: 테스트 코드
//        $coordinates = What3WordsUtil::address2Coordinates('index.home.raft');
//        $address = What3WordsUtil::coordinates2Address(51.521251,-0.203586);
//
//        return $app['twig']->render('/index.twig', array('coordinates' => $coordinates, 'address' => $address));

        return $app['twig']->render('/read_letter.twig');
    }
}
