<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function transform(User $user)
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ];
    }


    public function transformAll($users)
    {
        $arrayUser = [];
        foreach ($users as $user){
            $arrayUser[] = $this->transform($user);
        }
        return $arrayUser;
    }

    public function pagination($page,$max){

        $users = $this->createQueryBuilder('s')
            ->orderBy('s.id', 'ASC')
            ->setFirstResult($page)
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();

        $usersArray = [];
        foreach($users as $user){
            $usersArray[] = $this->transform($user);
        }

        return $usersArray;
    }

}
