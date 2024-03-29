<?php

namespace App\Controller\Pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DetailedServiceController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(): Response
    {

        return $this->render('home/services.html.twig', [
            'controller_name' => 'DetailedServiceController',
            'current_menu' => 'services'
        ]);
    }
}