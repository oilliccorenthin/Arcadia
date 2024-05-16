<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder, 
        private UserRepository $userRepository
    )
    {
       
    }

    public function load(ObjectManager $manager): void
    {
        $this->createRoles($manager);
        $this->createAdmin($manager);
    }

    private function createRoles(ObjectManager $manager): void
    {
        $roleAdmin = new Role();
        $roleAdmin->setLabel('ROLE_ADMIN');
        $manager->persist($roleAdmin);

        $roleEmployee = new Role();
        $roleEmployee->setLabel('ROLE_EMPLOYEE');
        $manager->persist($roleEmployee);

        $roleVeterinary = new Role();
        $roleVeterinary->setLabel('ROLE_VETERINARY');
        $manager->persist($roleVeterinary);

        $manager->flush();
        return;
    }

    private function createAdmin(ObjectManager $manager): void
    {
        $adminRole = $manager->getRepository(Role::class)->findOneBy(['label' => 'ROLE_ADMIN']);
        if (!$adminRole) {
            throw new \RuntimeException('ROLE_ADMIN role not found.');
        }

        $admin = new User();
        $admin
            ->setEmail('admin@arcadia.fr')
            ->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'))
            ->addRole($adminRole);
        
        $this->userRepository->addUserRole($admin, $adminRole);

        $manager->persist($admin);
        $manager->flush();
        return;
    }
}
