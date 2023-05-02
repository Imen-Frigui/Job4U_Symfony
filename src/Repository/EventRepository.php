<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EventCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByTitleAndCategory(string $title, EventCategory $category = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.title LIKE :title')
            ->setParameter('title', '%' . $title . '%');

        if ($category) {
            $qb->andWhere('e.eventCategory = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }

    public function getParticipantStatusCount($eventId, $status)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('count(p.id)')
            ->join('e.participants', 'p')
            ->where('e.id = :eventId')
            ->andWhere('p.status = :status')
            ->setParameter('eventId', $eventId)
            ->setParameter('status', $status);

        return $qb->getQuery()->getSingleScalarResult();
    }





    //    /**
    //     * @return Event[] Returns an array of Event objects
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

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
