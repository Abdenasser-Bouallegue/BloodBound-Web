<?php

namespace App\Repository;

use App\Entity\Achat;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Achat>
 *
 * @method Achat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achat[]    findAll()
 * @method Achat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achat::class);
    }

    public function save(Achat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Achat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countTicketsPurchasedForTicketId(Ticket $ticket) // ticket
    {
        $qb = $this->createQueryBuilder('p');
        
       // $qb->select('COUNT(p.ticket.stock)') // select from Achat , Count , selon Id ticket
       //    ->join('p.ticket', 't') // join() taati ticket , jointure entre ticket achat
       //    ->where('p.user = :user') 
       //    ->setParameter('user', $user);

        //return $qb->getQuery()->getSingleScalarResult();

        return $qb->select('COUNT(p.id)')
            ->where('p.ticket = :ticketId')
            ->setParameter('ticketId', $ticket)
            ->getQuery()
            ->getSingleScalarResult();
    }


//    /**
//     * @return Achat[] Returns an array of Achat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Achat
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
