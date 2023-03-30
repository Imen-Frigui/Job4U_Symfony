<?php

namespace App\Repository;

use App\Entity\Users;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Users>
 *
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    public function save(Users $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Users $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function searchByNom($Nom):array
   {
       return $this->createQueryBuilder('u')
           ->andWhere('u.Nom LIKE :nom')
           ->setParameter('nom', $Nom)
          
        
           ->getQuery()
           ->getResult()
       ;
   }


   public function searchByMail($Mail):object
   {
       return $this->createQueryBuilder('u')
           ->andWhere('u.Mail LIKE :Mail')
           ->setParameter('Mail', $Mail)
          
        
           ->getQuery()
           ->getResult()
       ;
   }

   public function searchtri($Nom):array
   {
       return $this->createQueryBuilder('u')
           ->andWhere('u.Nom LIKE :nom')
           ->setParameter('nom', $Nom)
          
           ->orderBy('u.Nom', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

   
  
   public function search($mots=Null){
    $query = $this->createQueryBuilder('u');
   
    if($mots != null){
        $query->andWhere('MATCH_AGAINST(u.Nom, u.Role) AGAINST (:mots boolean)>0')
            ->setParameter('mots', $mots);
    }
  
    return $query->getQuery()->getResult();
    }


   public function findAlltri()
   {
       return $this->findBy(array(), array('Nom' => 'ASC'));
   }
   

//    /**
//     * @return Users[] Returns an array of Users objects
//     */
   

//    public function findOneBySomeField($value): ?Users
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
