<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user', name: 'app_admin_user')]
class UserController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordEncoder;

    public function __construct(UserRepository $repository, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->repository = $repository;
        $this->doctrine = $doctrine;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $this->repository->findAll(),
            'current_menu' => 'admin',
        ]); 
    }

    
    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        //User and form creation
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $selectedRoles = $form->get('roleObjects')->getData();

            foreach ($selectedRoles as $role) {
                $userRepository->addUserRole($user, $role);
            }

            // Hashing password
            $hashedPassword = $this->passwordEncoder->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // Saving user in BDD
            $entitymanager = $this->doctrine->getManager();
            $entitymanager->persist($user);
            $entitymanager->flush();
            $this->addFlash('success', 'Créé avec succès !');

            // Sending email to the new User
            if ($this->getParameter('kernel.environment') !== 'prod') {
                $mail = (new TemplatedEmail())
                    ->to($user->getEmail())
                    ->from('gestion@zoo.arcadia.fr')
                    ->subject('Création de compte')
                    ->htmlTemplate('emails/new_user.html.twig')
                    ->context(['user' => $user]);
                $mailer->send($mail);
                $this->addFlash('success', 'Votre message a bien été envoyé');
            }

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'current_menu' => 'admin',
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $oldPassword = $user->getPassword();
        $user->setPassword('');
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $hashedPassword = $this->passwordEncoder->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            } else {
                $user->setPassword($oldPassword);
            }

            $selectedRoles = $form->get('roleObjects')->getData();
            foreach ($selectedRoles as $role) {
                $userRepository->addUserRole($user, $role);
            }
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', 'Modifié avec succès');

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'current_menu' => 'admin',
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: '_delete')]
    public function delete(Request $request, User $user): Response
    {
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->doctrine->getManager()->remove($user);
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', 'Modifié avec succès');
        }

        return $this->redirectToRoute('app_admin_user_index');
    }
}