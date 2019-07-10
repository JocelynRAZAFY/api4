<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\This;

class UserFixtures extends Fixture
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $users = [
            [
                'email' => 'rt1jocelyn@gmail.com',
                'username' => 'Joraz',
                'password' => '123',
                'role' => 'ROLE_ADMIN',
            ]
        ];
        if(count($this->userRepository->findAll()) == 0){
            foreach ($users as $usr){
                $user = new User();
                $user->setEmail($usr['email']);
                $user->setUsername($usr['username']);
                $user->addRole($usr['role']);
                $user->setPlainPassword($usr['password']);
                $user->setRegistrationDate(new \DateTime());
                $user->setValidationDate(new \DateTime());
                $user->setConfirmationToken(md5(uniqid()));
                $user->setEnabled(true);
                $manager->persist($user);
            }

            $manager->flush();
        }
    }
}
