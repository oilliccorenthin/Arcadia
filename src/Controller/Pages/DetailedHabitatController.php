<?php

namespace App\Controller\Pages;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DetailedHabitatController extends AbstractController
{
    #[Route('/habitats', name: 'app_habitats')]
    public function index(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        
        return $this->render('home/habitats.html.twig', [
            'controller_name' => 'DetailedHabitatController',
            'current_menu' => 'habitats'
        ]);
    }
}
