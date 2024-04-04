<?php

namespace App\Controller\Pages;

use App\Entity\Opinion;
use App\Form\OpinionType;
use App\Repository\OpinionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OpinionController extends AbstractController
{

    /**
     * @var OpinionRepository
     */
    private $repository;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    public function __construct(OpinionRepository $repository, ManagerRegistry $doctrine)
    {
        $this->repository = $repository;
        $this->doctrine = $doctrine;
    }

    #[Route('/opinion/new', name: 'app_opinion', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $opinion = new Opinion();
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $opinion->setIsVisible(false);
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($opinion);
            $entityManager->flush();
            $this->addFlash('success', 'Envoyé avec succès !');

            return $this->redirectToRoute('app_home');
        }



        return $this->render('home/opinion.html.twig', [
            'form' => $form,
            'controller_name' => 'OpinionController',
        ]);
    }
}
