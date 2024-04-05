<?php

namespace App\Repository;

use App\Entity\ChatVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatVersion>
 *
 * @method ChatVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatVersion[]    findAll()
 * @method ChatVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatVersion::class);
    }

    //    /**
    //     * @return ChatVersion[] Returns an array of ChatVersion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ChatVersion
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
