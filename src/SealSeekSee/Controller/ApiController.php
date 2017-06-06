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
    // https://sealseeksee.yoobato.com/letter/find
    const LETTER_CHECK_SHORTEN_URL = 'http://bit.ly/2sJjJXD';

    public function connect(Application $app)
    {
        /** @var $api \Silex\ControllerCollection */
        $api = $app['controllers_factory'];


        $api->post('/letter/write', array($this, 'writeLetter'));
        $api->post('/letter/find', array($this, 'findLetter'));
        $api->post('/letter/read', array($this, 'readLetter'));

        return $api;
    }

    public function writeLetter(Request $req, Application $app)
    {
        // [START] 파라미터 유효성 검사
        $sender_phone = $req->get('sender_phone', null);
        $receiver_phone = $req->get('receiver_phone', null);
        if (empty($receiver_phone)) {
            return new Response('편지를 받을 사람의 휴대전화 번호를 입력해주세요.', Response::HTTP_BAD_REQUEST);
        }

        $title = $req->get('title', null);
        if (empty($title)) {
            return new Response('편지의 제목을 입력해주세요.', Response::HTTP_BAD_REQUEST);
        }
        $message = $req->get('message', null);
        if (empty($message)) {
            return new Response('편지의 내용을 입력해주세요.', Response::HTTP_BAD_REQUEST);
        }

        $lat = $req->get('lat', null);
        $lng = $req->get('lng', null);
        if (empty($lat) || empty($lng)) {
            return new Response('편지를 남길 위치가 잘못 설정되었습니다.', Response::HTTP_BAD_REQUEST);
        }
        // [END] 파라미터 유효성 검사

        try {
            // 위도, 경도로 부터 What3Words 주소를 가지고 온다.
            $w3w_address = What3WordsUtil::coordinates2Address($lat, $lng);

            // Letter entity 생성
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

            return Response::create('편지를 남겼습니다! 편지의 암호 단어는 ' . $w3w_address . ' 입니다.', Response::HTTP_OK);
        } catch (Exception $e) {
            return Response::create($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findLetter(Request $req, Application $app)
    {
        $receiver_phone = $req->get('receiver_phone', '');
        if (empty($receiver_phone)) {
            return Response::create('휴대전화 번호를 입력해주세요.', Response::HTTP_BAD_REQUEST);
        }

        $word1 = $req->get('word1', '');
        $word2 = $req->get('word2', '');
        $word3 = $req->get('word3', '');
        if (empty($word1) || empty($word2) || empty($word3)) {
            return Response::create('암호 단어를 모두 입력해주세요.', Response::HTTP_BAD_REQUEST);
        }
        $w3w_address = $word1 . '.' . $word2 . '.' . $word3;

        try {
            $coordinates_bounds = What3WordsUtil::address2CoordinatesBounds($w3w_address);

            $letters = LetterFactory::findByReceiverPhoneAndCoordinatesBounds($receiver_phone,
                $coordinates_bounds['northeast']['lat'],
                $coordinates_bounds['northeast']['lng'],
                $coordinates_bounds['southwest']['lat'],
                $coordinates_bounds['southwest']['lng']
            );
            if (empty($letters)) {
                throw new \Exception('남겨진 편지가 없습니다. 휴대전화 번호와 암호 단어를 다시 확인해주세요!');
            }
            // 가장 최근 편지 1개만 사용한다.
            $letter = $letters[0];

            return $app->json(array('letter_meta' => $letter->exportMetadataDTO()));
        } catch (\Exception $e) {
            return Response::create($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function readLetter(Request $req, Application $app)
    {
        $letter_id = $req->get('letter_id', -1);
        $letter = LetterFactory::get($letter_id);
        if (empty($letter)) {
            return Response::create('편지가 없습니다.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $app->json(array('letter' => $letter));
    }
}
