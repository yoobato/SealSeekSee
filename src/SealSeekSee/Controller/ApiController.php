<?php
namespace SealSeekSee\Controller;

use Exception;
use SealSeekSee\Entity\LetterFactory;
use SealSeekSee\Util\SMSUtil;
use SealSeekSee\Util\What3WordsUtil;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        /**
         * @var $api \Silex\ControllerCollection
         */
        $api = $app['controllers_factory'];

        $api->post('/letter/write', array($this, 'writeLetter'));

        return $api;
    }

    public function writeLetter(Request $req, Application $app)
    {
        $lat = $req->get('lat', null);
        $lng = $req->get('lng', null);
        if (empty($lat) || empty($lng)) {
            return new Response('잘못된 위치값', Response::HTTP_BAD_REQUEST);
        }

        $sender_phone = $req->get('sender_phone', null);
        $receiver_phone = $req->get('receiver_phone', null);
        if (empty($receiver_phone)) {
            return new Response('받는 사람 번호 없음', Response::HTTP_BAD_REQUEST);
        }

        $title = $req->get('title', null);
        if (empty($receiver_phone)) {
            return new Response('편지 제목 없음', Response::HTTP_BAD_REQUEST);
        }
        $message = $req->get('message', null);
        if (empty($receiver_phone)) {
            return new Response('편지 내용 없음', Response::HTTP_BAD_REQUEST);
        }

        $w3w_address = What3WordsUtil::coordinates2Address($lat, $lng);
        if (empty($w3w_address)) {
            return new Response('주소 잘못됨', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            $letter = LetterFactory::create($sender_phone, $receiver_phone, $title, $message, $w3w_address, $lat, $lng);
            // TODO: 문자 아끼자
            //SMSUtil::send($receiver_phone, '편지(' . $title . ') 추가됨 (' . $w3w_address . ')');

            $app['session']->getFlashBag()->add('alert', array('success' => '편지가 추가되었습니다. (' . $w3w_address . ')'));

            return $app->redirect('/');
        } catch (Exception $e) {
            return Response::create('편지 생성 실패', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
