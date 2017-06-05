<?php
namespace SealSeekSee\Entity;

use SealSeekSee\DB\EntityManagerProvider;

class LetterFactory
{
    /**
     * @param $id
     * @return Letter
     */
    public static function get($id)
    {
        $em = EntityManagerProvider::getEntityManager();
        /** @var Letter $letter */
        $letter = $em->getRepository('SealSeekSee\Entity\Letter')
            ->findOneBy(array('id' => $id));
        return $letter;
    }

    /**
     * @param $receiver_phone
     * @param $w3w_address
     * @return Letter[]
     */
    public static function findListByReceiverPhoneAndWhat3WordsAddress($receiver_phone, $w3w_address)
    {
        $em = EntityManagerProvider::getEntityManager();
        $letters = $em->getRepository('SealSeekSee\Entity\Letter')->findBy(
            array('receiver_phone' => $receiver_phone, 'w3w_address' => $w3w_address),
            array('reg_date' => 'DESC')
        );
        return $letters;
    }

    /**
     * @param $receiver_phone
     * @param $w3w_address
     * @return Letter
     */
    public static function findLatestOneByReceiverPhoneAndWhat3WordsAddress($receiver_phone, $w3w_address)
    {
        $em = EntityManagerProvider::getEntityManager();
        /** @var Letter $letter */
        $letter = $em->getRepository('SealSeekSee\Entity\Letter')->findOneBy(
            array('receiver_phone' => $receiver_phone, 'w3w_address' => $w3w_address),
            array('reg_date' => 'DESC')
        );
        return $letter;
    }

    /**
     * @param $sender_phone
     * @param $receiver_phone
     * @param $title
     * @param $message
     * @param $w3w_address
     * @param $latitude
     * @param $longitude
     * @return Letter
     */
    public static function create($sender_phone, $receiver_phone, $title, $message, $w3w_address, $latitude, $longitude)
    {
        $letter = new Letter($sender_phone, $receiver_phone, $title, $message, $w3w_address, $latitude, $longitude);
        $em = EntityManagerProvider::getEntityManager();
        $em->persist($letter);
        $em->flush();
        return $letter;
    }
}
