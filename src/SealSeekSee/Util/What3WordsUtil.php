<?php
namespace SealSeekSee\Util;

// 참고 https://docs.what3words.com/api/v2/
class What3WordsUtil
{
    const WHAT_3_WORDS_API_KEY = '1MPXZL99';

    /**
     * What 3 Words API (Forward Geocoding)
     * 3단어 주소 => 위도, 경도
     *
     * @param $address
     * @return array
     */
    public static function address2Coordinates($address)
    {
        $query = http_build_query([
            'addr' => $address,
            'display' => 'minimal',
            'key' => static::WHAT_3_WORDS_API_KEY
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.what3words.com/v2/forward?' . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        
        return [
            'lat' => $response['geometry']['lat'],
            'lng' => $response['geometry']['lng']
        ];
    }

    /**
     * What 3 Words API (Reverse Geocoding)
     * 위도, 경도 => 3단어 주소
     *
     * @param $lat
     * @param $lng
     * @return string
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

        return $response['words'];
    }
}
