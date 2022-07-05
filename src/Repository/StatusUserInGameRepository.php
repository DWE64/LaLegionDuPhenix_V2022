<?php

namespace App\Repository;

use App\Entity\StatusUserInGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatusUserInGame>
 *
 * @method StatusUserInGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusUserInGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusUserInGame[]    findAll()
 * @method StatusUserInGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusUserInGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusUserInGame::class);
    }

    public function add(StatusUserInGame $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatusUserInGame $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return StatusUserInGame[] Returns an array of StatusUserInGame objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatusUserInGame
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
