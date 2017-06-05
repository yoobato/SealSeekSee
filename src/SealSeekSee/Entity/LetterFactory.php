<?php
namespace SealSeekSee\Entity;

use Doctrine\DBAL\Types\Type;
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
     * @param $northeast_lat
     * @param $northeast_lng
     * @param $southwest_lat
     * @param $southwest_lng
     * @return Letter[]
     */
    public static function findByReceiverPhoneAndCoordinatesBounds($receiver_phone, $northeast_lat, $northeast_lng, $southwest_lat, $southwest_lng)
    {
        $em = EntityManagerProvider::getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('l')
            ->from('SealSeekSee\Entity\Letter', 'l')
            ->where('l.receiver_phone = :receiver_phone')
            ->andWhere('l.latitude >= :min_latitude')
            ->andWhere('l.longitude >= :min_longitude')
            ->andWhere('l.latitude <= :max_latitude')
            ->andWhere('l.longitude <= :max_longitude')
            ->setParameter('receiver_phone', $receiver_phone, Type::STRING)
            ->setParameter('min_latitude', $southwest_lat, Type::DECIMAL)
            ->setParameter('min_longitude', $southwest_lng, Type::DECIMAL)
            ->setParameter('max_latitude', $northeast_lat, Type::DECIMAL)
            ->setParameter('max_longitude', $northeast_lng, Type::DECIMAL)
            ->orderBy('l.created_date', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Letter $letter
     * @return Letter
     */
    public static function updateOpenedDate($letter)
    {
        $letter->opened_date = date('Y-m-d H:i:s');

        $em = EntityManagerProvider::getEntityManager();
        $em->persist($letter);
        $em->flush();
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
