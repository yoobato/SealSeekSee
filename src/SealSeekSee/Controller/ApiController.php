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
    const LETTER_CHECK_SHORTEN_URL = 'http://bit.ly/2rBjiPM';

    public function connect(Application $app)
    {
        /** @var $api \Silex\ControllerCollection */
        $api = $app['controllers_factory'];

        $api->post('/letter/write', array($this, 'writeLetter'));

        return $api;
    }

    public function writeLetter(Request $req, Application $app)
    {
        $lat = $req->get('lat', null);
        $lng = $req->get('lng', null);
        if (empty($lat) || empty($lng)) {
            return new Response('편지를 남길 위치가 잘못 설정되었습니다.', Response::HTTP_BAD_REQUEST);
        }

        $sender_phone = $req->get('sender_phone', null);
        $receiver_phone = $req->get('receiver_phone', null);
        if (empty($receiver_phone)) {
            return new Response('편지를 받을 사람의 휴대전화 번호를 입력해주세요.', Response::HTTP_BAD_REQUEST);
        }

        $title = $req->get('title', null);
        if (empty($receiver_phone)) {
            return new Response('편지의 제목을 입력해주세요.', Response::HTTP_BAD_REQUEST);
        }
        $message = $req->get('message', null);
        if (empty($receiver_phone)) {
            return new Response('편지의 내용을 입력해주세요.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $w3w_address = What3WordsUtil::coordinates2Address($lat, $lng);

            $letter = LetterFactory::create($sender_phone, $receiver_phone, $title, $message, $w3w_address, $lat, $lng);
            if (empty($letter)) {
                throw new Exception('편지를 보내는 도중 오류가 발생하였습니다.');
            }

            // 편지 수신자에게 문자 보내기 (문자 내용이 길어질 수도 있어, 두번으로 나눠서 보낸다.)
            $sms_message = '* SealSeekSee *' . "\r\n" . '누군가 편지를 남겼어요~! 편지를 찾아주세요! ' . static::LETTER_CHECK_SHORTEN_URL;
            SMSUtil::send($receiver_phone, $sms_message);

            $w3w_address = '[ ' . str_replace('.', ', ', $w3w_address) . ' ]';
            $sms_message = '암호 단어는 ' . $w3w_address . ' 입니다!';
            SMSUtil::send($receiver_phone, $sms_message);

            // Flash 메세지
            $app['session']->getFlashBag()->add('alert', array('success' => '편지를 남겼습니다! 편지의 암호 단어는 ' . $w3w_address . ' 입니다.'));

            return $app->redirect('/');
        } catch (Exception $e) {
            return Response::create($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
