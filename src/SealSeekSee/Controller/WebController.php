<?php
namespace SealSeekSee\Controller;

use SealSeekSee\Entity\LetterFactory;
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

        // TODO: 나중에 post로 바꾸던가 해야한다
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

        $letter = LetterFactory::findLatestOneByReceiverPhoneAndWhat3WordsAddress($receiver_phone, $w3w_address);
        if ($letter == null) {
            return new Response('해당하는 편지가 없음', Response::HTTP_BAD_REQUEST);
        }

        return $app['twig']->render('check_letter_map.twig', array('letter' => $letter));
    }

    public function renderReadLetterPage(Request $req, Application $app)
    {
        $letter_id = $req->get('letter_id', '');
        $letter = LetterFactory::get($letter_id);
        if ($letter == null) {
            return new Response('잘못된 편지 ID');
        }
        return $app['twig']->render('/read_letter.twig', array('letter' => $letter));
    }
}
