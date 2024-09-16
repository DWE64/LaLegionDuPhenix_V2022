<?php
namespace App\Repository;

use App\Entity\StatusUserInGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
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

    public function remove(StatusUserInGame $statusUserInGame, bool $flush = false): void
    {
        $this->_em->remove($statusUserInGame);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function add(StatusUserInGame $statusUserInGame, bool $flush = false): void
    {
        $this->getEntityManager()->persist($statusUserInGame);

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
