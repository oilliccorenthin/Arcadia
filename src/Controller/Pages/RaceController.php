<?php

namespace App\Controller\Pages;

use App\Entity\Race;
use App\Form\RaceType;
use App\Repository\RaceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RaceController extends AbstractController
{
    /**
	 * @var RaceRepository
	 */
	private $repository;

	/**
	 * @var ManagerRegistry
	 */
	private $doctrine;

	public function __construct(RaceRepository $repository, ManagerRegistry $doctrine)
	{
		$this->repository = $repository;
		$this->doctrine = $doctrine;
	}

    #[Route('/admin/race', name: 'app_admin_race_index', methods: ['GET'])]
	public function index(): Response
	{
		return $this->render('admin/race/index.html.twig', [
			'races' => $this->repository->findAll(),
			'current_menu' => 'admin',
		]); 
	}

    #[Route('/admin/race/new', name: 'app_admin_race_new', methods: ['GET', 'POST'])]
	public function new(Request $request): Response
	{
		$race = new Race();
		$form = $this->createForm(RaceType::class, $race);
		$form->handleRequest($request);
		

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager = $this->doctrine->getManager();
			$entityManager->persist($race);
			$entityManager->flush();

			$this->addFlash('success', 'Crée avec succès !');

			return $this->redirectToRoute('app_admin_race_index');
		}

		return $this->render('admin/race/new.html.twig', [
			'race' => $race,
			'form' => $form->createView(),
			'current_menu' => 'admin',
		]);
	}

    #[Route('/admin/race/{id}/edit', name: 'app_admin_race_edit', methods: ['GET', 'POST'])]
	public function edit(Race $race, Request $request): Response
	{
		$form = $this->createForm(RaceType::class, $race);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager = $this->doctrine->getManager();
			

			$entityManager->flush();
			$this->addFlash('success', 'Modifié avec succès !');

			return $this->redirectToRoute('app_admin_race_index');
		}

		return $this->render('admin/race/edit.html.twig', [
			'race' => $race,
			'form' => $form->createView(),
			'current_menu' => 'admin',
		]);
	}

    #[Route('/admin/race/{id}', name: 'app_admin_race_delete')]
	public function delete(Race $race, Request $request): Response
	{
		if ($this->isCsrfTokenValid('delete'.$race->getId(), $request->request->get('_token'))) {
			$this->doctrine->getManager()->remove($race);
			$this->doctrine->getManager()->flush();
			$this->addFlash('success', 'Retiré avec succès');
		}

		return $this->redirectToRoute('app_admin_race_index');
	}
}