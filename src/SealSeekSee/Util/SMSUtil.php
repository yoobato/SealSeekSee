<?php
namespace SealSeekSee\Util;

use Exception;

// Cafe24 SMS 호스팅 사용
class SMSUtil
{
    const MAX_MESSAGE_LENGTH = 89;  // < 90byte 까지 가능

    public static function send($receiver_phone, $message)
    {
        // 휴대전화 번호 사이에 '-'를 넣어준다
        if (strpos($receiver_phone, '-') === false) {
            $new_phone = substr($receiver_phone, 0, 3);
            $new_phone .= '-';

            // 01x-xxx-xxxx
            if (strlen($receiver_phone) == 10) {
                $new_phone .= substr($receiver_phone, 3, 3);
                $new_phone .= '-';
                $new_phone .= substr($receiver_phone, 6, 4);
            } else if (strlen($receiver_phone) == 11) {
                // 010-xxxx-xxxx
                $new_phone .= substr($receiver_phone, 3, 4);
                $new_phone .= '-';
                $new_phone .= substr($receiver_phone, 7, 4);
            } else {
                throw new Exception('Invalid phone number');
            }

            $receiver_phone = $new_phone;
        }

        $sms_url = 'http://sslsms.cafe24.com/sms_sender.php';   // 전송요청 URL
//        $sms_url = 'https://sslsms.cafe24.com/sms_sender.php';  // HTTPS 전송요청 URL

        $sms['smsType'] = base64_encode('S');                   // 단문(SMS): S, 장문(LMS): L
        $sms['mode'] = base64_encode('1');                      // base64 사용시 모드값 1

        $sms['user_id'] = base64_encode(CAFE24_SMS_USER_ID);      // SMS 아이디
        $sms['secure'] = base64_encode(CAFE24_SMS_SECURE);        // 인증키

        $sms['rphone'] = base64_encode($receiver_phone);        // 수신번호 ('-' 포함)
        $sms['msg'] = base64_encode(stripslashes($message));    // 단문 메세지

        $sender_phone = explode('-', CAFE24_SMS_SENDER_PHONE);    // 발신번호
        $sms['sphone1'] = base64_encode($sender_phone[0]);      // 발신번호1
        $sms['sphone2'] = base64_encode($sender_phone[1]);      // 발신번호2
        $sms['sphone3'] = base64_encode($sender_phone[2]);      // 발신번호3

//        $sms['testflag'] = base64_encode('Y');                  // 테스트시 Y

        $host_info = explode('/', $sms_url);
        $host = $host_info[2];
        $path = $host_info[3];

        srand((double) microtime() * 1000000);
        $boundary = '---------------------' . substr(md5(rand(0, 32000)), 0, 10);

        // 헤더 생성
        $header = 'POST /' . $path . ' HTTP/1.0' . "\r\n";
        $header .= 'Host: ' . $host . "\r\n";
        $header .= 'Content-type: multipart/form-data, boundary=' . $boundary . "\r\n";

        // 본문 생성
        $data = '';
        foreach ($sms as $index => $value) {
            $data .= '--' . $boundary . "\r\n";
            $data .= 'Content-Disposition: form-data; name="' . $index . '"' . "\r\n";
            $data .= "\r\n" . $value . "\r\n";
            $data .= '--' . $boundary . "\r\n";
        }
        $header .= 'Content-length: ' . strlen($data) . "\r\n\r\n";

        $fp = fsockopen($host, 80);
        if ($fp) {
            fputs($fp, $header . $data);
            $response = '';
            while(!feof($fp)) {
                $response .= fgets($fp, 8192);
            }
            fclose($fp);

            $message = explode("\r\n\r\n", trim($response));
            $result = explode(',', $message[1]);

            $send_result = $result[0];      // 발송 결과
            $count = $result[1];            // 잔여 건수

            if ($send_result != 'success') {
                if ($send_result == 'reserved') {
                    $error_message = '예약 성공';
                } else if ($send_result == '-100') {
                    $error_message = '서버 에러';
                } else if ($send_result == '-101') {
                    $error_message = '변수 부족 에러';
                } else if ($send_result == '-102') {
                    $error_message = '인증 에러';
                } else if ($send_result == '-105') {
                    $error_message = '예약 시간 에러';
                } else if ($send_result == '-110') {
                    $error_message = '1000건 이상 발송 불가';
                } else if ($send_result == '-114') {
                    $error_message = '등록/인증되지 않은 발신번호';
                } else if ($send_result == '-201') {
                    $error_message = 'sms 건수 부족 에러';
                } else if ($send_result == '-202') {
                    $error_message = '문자 \'됬\'은 사용불가능한 문자입니다';
                } else if ($send_result == '-203') {
                    $error_message = 'sms 대량 발송 에러';
                } else if ($send_result == '0001') {
                    $error_message = '서비스 번호 오류';
                } else if ($send_result == '0002') {
                    $error_message = '메시지 구성 결여';
                } else if ($send_result == '0003') {
                    $error_message = '메시지 포맷 오류';
                } else if ($send_result == '0004') {
                    $error_message = '메시지 body길이 오류';
                } else if ($send_result == '0005') {
                    $error_message = 'Connect 필요';
                } else if ($send_result == '0099') {
                    $error_message = '기타 오류(DB오류시스템장애)';
                } else if ($send_result == '0044') {
                    $error_message = '스팸메시지 차단(배팅, 바카라, 도박, 섹스, liveno1 ,카지노 등을 포함한 스팸메시지는 발송이 실패됩니다)';
                } else if ($send_result == '3201') {
                    $error_message = '발송시각 오류';
                } else if ($send_result == '3202') {
                    $error_message = '폰넘버 오류';
                } else if ($send_result == '3203') {
                    $error_message = 'SMS 메시지 Base64 Encoding 오류';
                } else if ($send_result == '3204') {
                    $error_message = 'CallBack메시지 Base64 Encoding 오류';
                } else if ($send_result == '3205') {
                    $error_message = '번호형식 오류';
                } else if ($send_result == '3206') {
                    $error_message = '전송 성공';
                } else if ($send_result == '3207') {
                    $error_message = '비가입자 결번 서비스정지';
                } else if ($send_result == '3208') {
                    $error_message = '단말기 Power-off 상태';
                } else if ($send_result == '3209') {
                    $error_message = '음영';
                } else if ($send_result == '3210') {
                    $error_message = '단말기 메시지 FULL';
                } else if ($send_result == '3211') {
                    $error_message = '기타에러(이통사)';
                } else if ($send_result == '3214') {
                    $error_message = '기타에러(무선망)';
                } else if ($send_result == '3213') {
                    $error_message = '번호이동관련';
                } else if ($send_result == '3217') {
                    $error_message = '조합메시지 형식오류';
                } else if ($send_result == '3218') {
                    $error_message = '메시지 중복 오류';
                } else if ($send_result == '3219') {
                    $error_message = '월 송신건수 초과';
                } else if ($send_result == '3220') {
                    $error_message = 'UNKNOWN';
                } else if ($send_result == '3221') {
                    $error_message = '착신번호 에러(자리수 에러)';
                } else if ($send_result == '3222') {
                    $error_message = '착신번호 에러(없는 국번)';
                } else if ($send_result == '3223') {
                    $error_message = '수신거부 메시지 부분 없음';
                } else if ($send_result == '3224') {
                    $error_message = '21시 이후 광고';
                } else {
                    $error_message = $send_result;
                }

                throw new Exception('[SMS] ' . $error_message);
            }
        } else {
            throw new Exception('[SMS] Connection failed');
        }
    }
}
