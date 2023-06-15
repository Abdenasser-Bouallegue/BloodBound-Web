<?php

namespace App\Repository;

use App\Entity\Emergency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emergency>
 *
 * @method Emergency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emergency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emergency[]    findAll()
 * @method Emergency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmergencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emergency::class);
    }

    public function save(Emergency $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Emergency $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getEmergencyCountByBloodType()
    {
        return $this->createQueryBuilder('e')
            ->select('e.bloodType, COUNT(e) as count')
            ->groupBy('e.bloodType')
            ->getQuery()
            ->getResult();
    }
    
//    /**
//     * @return Emergency[] Returns an array of Emergency objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Emergency
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
