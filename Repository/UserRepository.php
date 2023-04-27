<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
// public function findByEmail($email)
// {
//     $entityManager = $this->getEntityManager();
//     $query = $entityManager ->createQueryBuilder()
//         ->select('u.email', 'us.email')
//         ->from(User::class, 'u')
//         ->where('u.email=:email')
//         ->setParameter('email', $email)
//     ->innerJoin(Users::class, 'us', 'WITH','u.email = us.email');


//     return $query->getQuery()->getResult();
// }
public function search($mots=Null){
    $query = $this->createQueryBuilder('u');
   
    if($mots != null){
        $query->andWhere('MATCH_AGAINST(u.Nom) AGAINST (:mots boolean)>0')
            ->setParameter('mots', $mots);
    }
  
    return $query->getQuery()->getResult();
    }


   public function findAlltri()
   {
       return $this->findBy(array(), array('Nom' => 'ASC'));
   }


   public function findByEmailUser($email)
   {
       //     $entityManager = $this->getEntityManager();
       //    return $entityManager ->createQueryBuilder()
       //         ->select( 'us.Mail')
       //         ->from(Users::class, 'us')
       //         ->where('us.Mail=:Mail')
       //         ->setParameter('Mail', $email)->getQuery()->getResult();
       $entityManager = $this->getEntityManager();
       $userRepository = $entityManager->getRepository(User::class);

       $queryBuilder = $userRepository->createQueryBuilder('u');
       $queryBuilder->where('u.email = :email');
       $queryBuilder->setParameter('email', $email);

      return  $user = $queryBuilder->getQuery()->getOneOrNullResult();
   }



}
