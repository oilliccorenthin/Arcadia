<?php

namespace App\Controller\Pages;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\AnimalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimalController extends AbstractController
{   
	/**
	 * @var AnimalRepository
	 */
	private $repository;

	/**
	 * @var ManagerRegistry
	 */
	private $doctrine;

	public function __construct(AnimalRepository $repository, ManagerRegistry $doctrine)
	{
		$this->repository = $repository;
		$this->doctrine = $doctrine;
	}


	#[Route('/animal/{id}', name: 'app_animal')]
	public function show(Animal $animal): Response
	{
		$views = $animal->getViews();
		$animal->setViews($views + 1);
		$entityManager = $this->doctrine->getManager();
		$entityManager->flush();
		$user = $this->getUser();
        if ($user) {
            $roles = $user->getRoles();
        } else {
            $roles = [];
        }

		return $this->render('home/animal.html.twig', [
			'roles' => $roles,
			'animal' => $animal,
			'current_menu' => 'habitat',
		]);
	}

	

	#[Route('/admin/animal', name: 'app_admin_animal_index', methods: ['GET'])]
	public function index(): Response
	{
		return $this->render('admin/animal/index.html.twig', [
			'animals' => $this->repository->findAll(),
			'current_menu' => 'admin',
		]); 
	}

	#[Route('/admin/views', name: 'app_admin_view_index', methods: ['GET'])]
	public function views(): Response
	{
		return $this->render('admin/animal/views.html.twig', [
			'animals' => $this->repository->findAll(),
			'current_menu' => 'admin',
		]); 
	}

	#[Route('/admin/feed', name: 'app_admin_feed_index', methods: ['GET'])]
	public function feed(): Response
	{
		return $this->render('admin/animal/feed.html.twig', [
			'animals' => $this->repository->findAll(),
			'current_menu' => 'admin',
		]); 
	}

	#[Route('/admin/animal/new', name: 'app_admin_animal_new', methods: ['GET', 'POST'])]
	public function new(Request $request): Response
	{
		$animal = new Animal();
		$form = $this->createForm(AnimalType::class, $animal);
		$form->handleRequest($request);
		

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager = $this->doctrine->getManager();
			$entityManager->persist($animal);
			$entityManager->flush();

			$this->addFlash('success', 'Ajouté avec succès !');

			return $this->redirectToRoute('app_admin_animal_index');
		}

		return $this->render('admin/animal/new.html.twig', [
			'animal' => $animal,
			'form' => $form->createView(),
			'current_menu' => 'admin',
		]);
	}

	#[Route('/admin/animal/{id}/edit', name: 'app_admin_animal_edit', methods: ['GET', 'POST'])]
	public function edit(Animal $animal, Request $request): Response
	{
		$form = $this->createForm(AnimalType::class, $animal);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager = $this->doctrine->getManager();
			

			$entityManager->flush();
			$this->addFlash('success', 'Modifié avec succès !');

			return $this->redirectToRoute('app_admin_animal_index');
		}

		return $this->render('admin/animal/edit.html.twig', [
			'animal' => $animal,
			'form' => $form->createView(),
			'current_menu' => 'admin',
		]);
	}



	#[Route('/admin/animal/{id}/reset-views', name: 'app_admin_animal_reset_views', methods: ['POST'])]
	public function resetViews(Animal $animal, EntityManagerInterface $entityManager): Response
	{
		$animal->setViews(0);
		$entityManager->flush();

		$this->addFlash('success', 'Le nombre de vues de '. $animal->getName() .' a été réinitialisé avec succès.');

		return $this->redirectToRoute('app_admin_view_index');
	}

	#[Route('/admin/animal/{id}/update-last-fed', name: 'app_admin_animal_update_last_fed', methods: ['POST'])]
	public function updateLastFedAt(Animal $animal, EntityManagerInterface $entityManager): Response
	{
		$animal->setLastFedAt(new \DateTime());
		$entityManager->flush();

		$this->addFlash('success', 'L\'heure du dernier repas de '. $animal->getName() .' a été mise à jour avec succès.');

		return $this->redirectToRoute('app_admin_feed_index');
	}

	#[Route('/admin/animal/{id}', name: 'app_admin_animal_delete')]
	public function delete(Animal $animal, Request $request): Response
	{
		if ($this->isCsrfTokenValid('delete'.$animal->getId(), $request->request->get('_token'))) {
			$this->doctrine->getManager()->remove($animal);
			$this->doctrine->getManager()->flush();
			$this->addFlash('success', 'Retiré avec succès');
		}

		return $this->redirectToRoute('app_admin_animal_index');
	}
}