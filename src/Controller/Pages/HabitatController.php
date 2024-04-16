<?php

namespace App\Controller\Pages;

use App\Entity\Habitat;
use App\Entity\Image;
use App\Repository\HabitatRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\HabitatType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HabitatController extends AbstractController
{   
    /**
     * @var HabitatRepository
     */
    private $repository;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    public function __construct(HabitatRepository $repository, ManagerRegistry $doctrine)
    {
        $this->repository = $repository;
        $this->doctrine = $doctrine;
    }


    #[Route('/habitat', name: 'app_habitat')]
    public function show(): Response
    {
        
        return $this->render('home/habitat.html.twig', [
            'habitats' => $this->repository->findAll(),
            'current_menu' => 'habitat'
        ]);
    }

    #[Route('/admin/habitat', name: 'app_admin_habitat_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/habitat/index.html.twig', [
            'habitats' => $this->repository->findAll(),
            'current_menu' => 'admin',
        ]); 
    }



    #[Route('/admin/habitat/new', name: 'app_admin_habitat_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $habitat = new Habitat();
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);
        $debugData = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();

            // Gérer les images téléchargées
            foreach ($habitat->getImages() as $image) {
                $newImage = new Image();
                $newImage->setHabitat($habitat);
                $newImage->setImageFile($image->getImageFile());
                $entityManager->persist($newImage);
                $this->addFlash('success', 'Image téléchargée avec succès !');
            }

            $entityManager->persist($habitat);
            $entityManager->flush();

            $this->addFlash('success', 'Envoyé avec succès !');

            return $this->redirectToRoute('app_admin_habitat_index');
        }

        return $this->render('admin/habitat/new.html.twig', [
            'habitat' => $habitat,
            'form' => $form->createView(),
            'current_menu' => 'admin',
            'debugData' => $debugData,
        ]);
    }

    #[Route('/admin/habitat/{id}/edit', name: 'app_admin_habitat_edit', methods: ['GET', 'POST'])]
    public function edit(Habitat $habitat, Request $request): Response
    {
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();

            // Gérer les images téléchargées
            foreach ($habitat->getImages() as $image) {
                /** @var UploadedFile $imageFile */
                $imageFile = $image->getImageFile();
                if ($imageFile) {
                    $image->setImageName($imageFile->getClientOriginalName());
                }
                $entityManager->persist($image);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Modifié avec succès !');

            return $this->redirectToRoute('app_admin_habitat_index');
        }

        return $this->render('admin/habitat/edit.html.twig', [
            'habitat' => $habitat,
            'form' => $form->createView(),
            'current_menu' => 'admin',
        ]);
    }

    #[Route('/admin/habitat/{id}', name: 'app_admin_habitat_delete')]
    public function delete(Habitat $habitat, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$habitat->getId(), $request->request->get('_token'))) {
            $this->doctrine->getManager()->remove($habitat);
            $this->doctrine->getManager()->flush();
            $this->addFlash('success', 'Supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_habitat_index');
    }
}