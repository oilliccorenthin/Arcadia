<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user) {
            $roles = $user->getRoles();
        } else {
            $roles = [];
        }


        return $this->render('admin/home.html.twig', [
            'controller_name' => 'AdminController',
            'current_menu' => 'admin',
            'user' => $user,
            'roles' => $roles
        ]);
    }
}