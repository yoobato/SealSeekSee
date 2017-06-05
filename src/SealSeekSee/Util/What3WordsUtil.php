<?php
namespace SealSeekSee\Util;

// 참고 https://docs.what3words.com/api/v2/
use Exception;

class What3WordsUtil
{
    const WHAT_3_WORDS_API_KEY = '1MPXZL99';

    /**
     * What 3 Words API (Forward Geocoding)
     * 3단어 주소 => 위도, 경도
     *
     * @param $address
     * @return array
     * @throws Exception
     */
    public static function address2Coordinates($address)
    {
        $query = http_build_query([
            'addr' => $address,
            'display' => 'full',
            'key' => static::WHAT_3_WORDS_API_KEY
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.what3words.com/v2/forward?' . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        if (isset($response['bounds'])) {
            return $response['bounds'];
        } else {
            // 존재하지 않는 단어 주소를 입력한 경우
            throw new Exception('편지의 암호 단어를 잘못 입력하셨습니다.');
        }
    }

    /**
     * What 3 Words API (Reverse Geocoding)
     * 위도, 경도 => 3단어 주소
     *
     * @param $lat
     * @param $lng
     * @return string
     * @throws Exception
     */
    public static function coordinates2Address($lat, $lng)
    {
        $query = http_build_query([
            'coords' => $lat . ',' . $lng,
            'display' => 'minimal',
            'key' => static::WHAT_3_WORDS_API_KEY
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.what3words.com/v2/reverse?' . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        if (isset($response['words']) && !empty($response['words'])) {
            return $response['words'];
        } else {
            // 존재하지 않는 위도, 경도를 입력한 경우
            throw new Exception('편지를 남길 위치가 잘못 설정되었습니다.');
        }
    }
}
