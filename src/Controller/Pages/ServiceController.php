<?php

namespace App\Controller\Pages;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{

    /**
     * @var ServiceRepository
     */
    private $repository;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    public function __construct(ServiceRepository $repository, ManagerRegistry $doctrine)
    {
        $this->repository = $repository;
        $this->doctrine = $doctrine;
    }

    #[Route('/service', name: 'app_service', methods: ['GET'])]
    public function show(): Response
    {

        return $this->render('home/service.html.twig', [
            'services' => $this->repository->findAll(),
            'current_menu' => 'service'
        ]);
    }

    #[Route('/admin/service', name: 'app_admin_service_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/service/index.html.twig', [
            'services' => $this->repository->findAll(),
            'current_menu' => 'admin',
        ]); 
    }

    #[Route('/admin/service/new', name: 'app_admin_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'Envoyé avec succès !');

            return $this->redirectToRoute('app_admin_service_index');
        }

        return $this->render('admin/service/new.html.twig', [
            'form' => $form,
            'current_menu' => 'admin',
            'controller_name' => 'OpinionController',
        ]);
    }

    #[Route('/admin/service/{id}/edit', name: 'app_admin_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);     

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', 'Modifié avec succès !');

            return $this->redirectToRoute('app_service_index');
        }

        return $this->render('admin//edit.html.twig', [
            'service' => $service,
            'current_menu' => 'admin',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/service/{id}', name: 'app_admin_service_delete')]
    public function delete(Request $request, Service $service): Response
    {
        if (!$service) {
            throw $this->createNotFoundException('Service not found');
        }
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $this->doctrine->getManager()->remove($service);
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', 'Supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_service_index');
    }
}