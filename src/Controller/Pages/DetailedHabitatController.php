<?php

namespace App\Controller\Pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailedHabitatController extends AbstractController
{
    #[Route('/habitats', name: 'app_habitats')]
    public function index(): Response
    {
        return $this->render('home/habitats.html.twig', [
            'controller_name' => 'DetailedHabitatController',
            'current_menu' => 'habitats'
        ]);
    }
}