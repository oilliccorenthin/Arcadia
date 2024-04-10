<?php

namespace App\Controller\Pages;

use App\Entity\Opinion;
use App\Form\OpinionType;
use App\Repository\OpinionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

    #[Route('/admin/opinion', name: 'app_admin_opinion_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/opinion/index.html.twig', [
            'opinions' => $this->repository->findAll(),
            'current_menu' => 'admin',
        ]); 
    }

    #[Route('/admin/opinion/{id}/edit', name: 'app_admin_opinion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Opinion $opinion): Response
    {
        $form = $this->createFormBuilder($opinion)
        ->add('is_visible', CheckboxType::class, [
            'required' => false,
        ])
        ->getForm();

        $form->handleRequest($request);     

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', 'Modifié avec succès !');

            return $this->redirectToRoute('app_admin_opinion_index');
        }

        return $this->render('admin/opinion/edit.html.twig', [
            'opinion' => $opinion,
            'current_menu' => 'admin',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/opinion/{id}', name: 'app_admin_opinion_delete')]
    public function delete(Request $request, Opinion $opinion): Response
    {
        if (!$opinion) {
            throw $this->createNotFoundException('opinion not found');
        }
        if ($this->isCsrfTokenValid('delete'.$opinion->getId(), $request->request->get('_token'))) {
            $this->doctrine->getManager()->remove($opinion);
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', 'Supprimé avec succès');
        }

        return $this->redirectToRoute('app_opinion_index');
    }
}
