<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $passwordEncoder;

    private $userRepository;

    private $roleRepository;

    private $doctrine;

    public function __construct(UserPasswordHasherInterface $passwordEncoder,ManagerRegistry $doctrine, UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->doctrine = $doctrine;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_admin');
        }

        $roles = $this->roleRepository->findAll();
        if (empty($roles)) {
            $this->createRoles();
        }

        $users = $this->userRepository->findAll();
        if (empty($users)) {
            $this->createDefaultAdmin($this->userRepository);
            $this->addFlash('warning', 'Un administrateur par defaut a été créé. Vous pouvez vous connecter avec les identifiants de la ddocumentation. Pensez à créer un compte administrateur et supprimer celui-ci.');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private function createRoles(): void
    {
        $entityManager = $this->doctrine->getManager();
        
        try {
            $roleAdmin = new Role();
            $roleAdmin->setLabel('ROLE_ADMIN');
            $entityManager->persist($roleAdmin);

            $roleEmployee = new Role();
            $roleEmployee->setLabel('ROLE_EMPLOYEE');
            $entityManager->persist($roleEmployee);

            $roleVeterinary = new Role();
            $roleVeterinary->setLabel('ROLE_VETERINARY');
            $entityManager->persist($roleVeterinary);

            $entityManager->flush();
            
        } catch (\Exception $e) {
            throw new \Exception('Error while creating roles : ' . $e->getMessage());
        }
    }

    private function createDefaultAdmin($userRepository): void
    {
            $entityManager = $this->doctrine->getManager();
            try {
                $admin = new User();
                $admin->setEmail('admin@arcadia.fr');
                $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'Password1234'));
                $userRepository->addUserRole($admin, 'ROLE_ADMIN');
                $entityManager->persist($admin);
                $entityManager->flush();
            } catch (\Exception $e) {
                throw new \Exception('Error while creating default admin : ' . $e->getMessage());
            }
    }

}
