<?php
namespace SealSeekSee\Entity;

use SealSeekSee\DB\EntityManagerProvider;

class LetterFactory
{
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


//    /**
//     * @return Memo[]
//     */
//    public static function findAll()
//    {
//        $em = EntityManagerProvider::getEntityManager();
//
//        $qb = $em->createQueryBuilder();
//        $qb->select('m')
//            ->from('MemoRise\Entity\Memo', 'm')
//            ->leftJoin('MemoRise\Entity\MemoGroup', 'mg', Expr\Join::WITH, 'm.group_id = mg.id')
//            ->orderBy('m.reg_date', 'DESC')
//            ->addOrderBy('m.seq', 'DESC');
//
//        return $qb->getQuery()->getResult();
//    }
//
//    /**
//     * @param $date
//     * @return Memo[]
//     */
//    public static function findListFromSinceDate($date)
//    {
//        $em = EntityManagerProvider::getEntityManager();
//
//        $qb = $em->createQueryBuilder();
//        $qb->select('m')
//            ->from('MemoRise\Entity\Memo', 'm')
//            ->leftJoin('MemoRise\Entity\MemoGroup', 'mg', Expr\Join::WITH, 'm.group_id = mg.id')
//            ->where('m.reg_date > :reg_date')
//            ->setParameter('reg_date', $date, Type::STRING)
//            ->orderBy('m.reg_date', 'DESC')
//            ->addOrderBy('m.seq', 'DESC');
//
//        return $qb->getQuery()->getResult();
//    }
//
//    /**
//     * @param $user_id
//     * @return Memo[]
//     */
//    public static function findListByUserId($user_id)
//    {
//        $em = EntityManagerProvider::getEntityManager();
//
//        $qb = $em->createQueryBuilder();
//        $qb->select('mg')
//            ->from('MemoRise\Entity\Memo', 'm')
//            ->join('MemoRise\Entity\MemoGroup', 'mg', Expr\Join::WITH, 'mg.id = m.group_id')
//            ->where('m.user_id = :user_id')
//            ->setParameter('user_id', $user_id, Type::INTEGER)
//            ->orderBy('m.reg_date', 'DESC')
//            ->addOrderBy('m.seq', 'DESC');
//
//        return $qb->getQuery()->getResult();
//    }
//
//    /**
//     * @param $min_latitude
//     * @param $min_longitude
//     * @param $max_latitude
//     * @param $max_longitude
//     * @param $user_id
//     * @param $offset
//     * @param $limit
//     * @param bool $exclude_user
//     * @return Memo[]
//     */
//    public static function findListByCoordinate($min_latitude, $min_longitude, $max_latitude, $max_longitude, $user_id, $offset, $limit, $exclude_user = false)
//    {
//        $em = EntityManagerProvider::getEntityManager();
//
//        $qb = $em->createQueryBuilder();
//        $qb->select('mg')
//            ->from('MemoRise\Entity\Memo', 'm')
//            ->join('MemoRise\Entity\MemoGroup', 'mg', Expr\Join::WITH, 'mg.id = m.group_id')
//            ->where('mg.latitude >= :min_latitude')
//            ->andWhere('mg.longitude >= :min_longitude')
//            ->andWhere('mg.latitude <= :max_latitude')
//            ->andWhere('mg.longitude <= :max_longitude')
//            ->setParameter('min_latitude', $min_latitude, Type::DECIMAL)
//            ->setParameter('min_longitude', $min_longitude, Type::DECIMAL)
//            ->setParameter('max_latitude', $max_latitude, Type::DECIMAL)
//            ->setParameter('max_longitude', $max_longitude, Type::DECIMAL)
//            ->orderBy('m.reg_date', 'DESC')
//            ->addOrderBy('m.seq', 'DESC')
//            ->setFirstResult($offset)
//            ->setMaxResults($limit);
//
//        if ($exclude_user) {
//            $qb->andWhere('m.user_id != :user_id');
//            $qb->andWhere('mg.is_public = 1');
//        } else {
//            $qb->andWhere('m.user_id = :user_id');
//        }
//        $qb->setParameter('user_id', $user_id, Type::INTEGER);
//
//        return $qb->getQuery()->getResult();
//    }
