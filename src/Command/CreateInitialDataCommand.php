<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;

#[AsCommand(
    name: 'app:create-initial-data',
    description: 'Create initial roles and admin user',
)]
class CreateInitialDataCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        // No need for arguments or options for this command
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->createRoles();
        $this->createAdmin();

        $io->success('Initial data created successfully.');

        return Command::SUCCESS;
    }

    private function createRoles(): void
    {
        $roles = ['ROLE_ADMIN', 'ROLE_EMPLOYEE', 'ROLE_VETERINARY'];
        
        foreach ($roles as $roleLabel) {
            $role = new Role();
            $role->setLabel($roleLabel);
            $this->entityManager->persist($role);
        }

        $this->entityManager->flush();
    }

    private function createAdmin(): void
    {
        $adminRole = $this->entityManager->getRepository(Role::class)->findOneBy(['label' => 'ROLE_ADMIN']);
        if (!$adminRole) {
            throw new \RuntimeException('ROLE_ADMIN role not found.');
        }

        $adminPassword = getenv('ADMIN_PASSWORD');
        if (!$adminPassword) {
            throw new \RuntimeException('ADMIN_PASSWORD environment variable not set.');
        }

        $admin = new User();
        $admin
            ->setEmail('admin@arcadia.fr')
            ->setPassword($this->passwordHasher->hashPassword($admin, $adminPassword))
            ->addRole($adminRole);
        
        $this->userRepository->addUserRole($admin, $adminRole);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();
    }
}
