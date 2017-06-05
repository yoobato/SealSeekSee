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
        /** @var $web \Silex\ControllerCollection */
        $web = $app['controllers_factory'];

        $web->get('/', function () use ($app) {
            return $app['twig']->render('/index.twig');
        });
        $web->get('/letter/write', function () use ($app) {
            return $app['twig']->render('/write_letter.twig');
        });
        $web->get('/letter/check', function () use ($app) {
            return $app['twig']->render('/check_letter.twig');
        });
        $web->post('/letter/check/map', array($this, 'renderCheckLetterMapPage'));
        $web->post('/letter/read', array($this, 'renderReadLetterPage'));

        return $web;
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

        try {
            $bounds = What3WordsUtil::address2Coordinates($w3w_address);

            $letters = LetterFactory::findByReceiverPhoneAndCoordinatesBounds(
                $receiver_phone,
                $bounds['northeast']['lat'],
                $bounds['northeast']['lng'],
                $bounds['southwest']['lat'],
                $bounds['southwest']['lng']
            );

            if (empty($letters)) {
                throw new \Exception('편지 없음');
            }
            return $app['twig']->render('check_letter_map.twig', array('letter' => $letters[0]));
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
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
