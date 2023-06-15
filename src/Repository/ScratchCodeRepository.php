<?php

namespace App\Repository;

use App\Entity\ScratchCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScratchCode>
 *
 * @method ScratchCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScratchCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScratchCode[]    findAll()
 * @method ScratchCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScratchCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScratchCode::class);
    }

    public function save(ScratchCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ScratchCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCode(string $code): ?ScratchCode
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->where('sc.code = :code')
           ->setParameter('code', $code);
        
        return $qb->getQuery()->getOneOrNullResult();
    }

//    /**
//     * @return ScratchCode[] Returns an array of ScratchCode objects
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

//    public function findOneBySomeField($value): ?ScratchCode
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
