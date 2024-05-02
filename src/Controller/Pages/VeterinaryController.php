<?php

namespace App\Controller\Pages;

use App\Entity\Animal;
use App\Entity\RapportVeterinaire;
use App\Form\VeterinaryType;
use App\Repository\AnimalRepository;
use App\Repository\RapportVeterinaireRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VeterinaryController extends AbstractController
{
    /**
	 * @var RapportVeterinaireRepository
	 */
	private $repository;

    /**
	 * @var AnimalRepository
	 */
	private $animalRepository;

	/**
	 * @var ManagerRegistry
	 */
	private $doctrine;

	public function __construct(RapportVeterinaireRepository $repositry, AnimalRepository $animalRepository, ManagerRegistry $doctrine)
	{
        $this->repository = $repositry;
		$this->animalRepository = $animalRepository;
		$this->doctrine = $doctrine;
	}

    #[Route('/report', name: 'app_admin_report_index')]
    public function index(): Response
    {
        return $this->render('admin/veterinary/index.html.twig', [
            'animals' => $this->animalRepository->findAll(),
            'current_menu' => 'admin',
        ]);
    }

    #[Route('/admin/animal/{id}/report/new', name: 'app_admin_report_new')]
	public function new(Animal $animal, Request $request): Response
	{
        $report = new RapportVeterinaire();
        $report->setAnimal($animal);
        $form = $this->createForm(VeterinaryType::class, $report, [
            'animal_state' => $animal->getState(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($report);
            $entityManager->flush();

            return $this->redirectToRoute('app_animal', ['id' => $animal->getId()]);
        }

		return $this->render('admin/veterinary/new.html.twig', [
            'form' => $form->createView(),
			'animal' => $animal,
			'current_menu' => 'admin',
		]);
	}

    #[Route('/admin/animal/report/{reportId}/edit', name: 'app_admin_report_edit')]
    public function edit(int $reportId, Request $request): Response
    {
        $report = $this->repository->find($reportId);
        $animal = $report->getAnimal();
        $animalId = $animal->getId();
        $form = $this->createForm(VeterinaryType::class, $report, [
            'animal_state' => $animal->getState(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $entityManager = $this->doctrine->getManager();
            $entityManager->flush();


            return $this->redirectToRoute('app_animal', ['id' => $animalId]);
        }

        return $this->render('admin/veterinary/edit.html.twig', [
            'form' => $form->createView(),
            'animal' => $animal,
            'report' => $report,
            'current_menu' => 'admin',
        ]);
    }

    #[Route('/admin/animal/report/{reportId}', name: 'app_admin_report_delete')]
    public function delete(int $reportId, Request $request): Response
    {
        $entityManager = $this->doctrine->getManager();
        $report = $entityManager->getRepository(RapportVeterinaire::class)->find($reportId);

        if (!$report) {
            throw $this->createNotFoundException('Rapport vétérinaire non trouvé');
        }

        $animal = $report->getAnimal();
        if ($animal) {
            $animal->setVeterinaryReport(null);
            $entityManager->persist($animal);
        }

        if ($this->isCsrfTokenValid('delete'.$report->getId(), $request->request->get('_token'))) {
            $entityManager->remove($report);
            $entityManager->flush();
            $this->addFlash('success', 'Rapport vétérinaire retiré avec succès');
        }

        return $this->redirectToRoute('app_admin_report_index');
    }

}
