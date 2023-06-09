<?php

namespace App\Repository;

use App\Entity\Postulation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Postulation>
 *
 * @method Postulation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Postulation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Postulation[]    findAll()
 * @method Postulation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostulationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Postulation::class);
    }

    public function save(Postulation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Postulation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function countPostulations(): int
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('COUNT(p.idPos)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    //    /**
    //     * @return Postulation[] Returns an array of Postulation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Postulation
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByCriteria(Postulation $postulation): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($postulation->getEmail()) {
            $qb->andWhere('p.email LIKE :email')
                ->setParameter('email', '%' . $postulation->getEmail() . '%');
        }

        if ($postulation->getCreator()) {
            $qb->andWhere('p.creator = :creator')
                ->setParameter('creator', $postulation->getCreator());
        }

        return $qb->getQuery()->getResult();
    }
    public function findByCreator(User $creator): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.creator = :creator')
            ->setParameter('creator', $creator)
            ->getQuery()
            ->getResult();
    }
}
