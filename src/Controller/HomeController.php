<?php

namespace App\Controller;

use App\Repository\HabitatRepository;
use App\Repository\OpinionRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @var HabitatRepository
     */
    private $habitatRepository;

    /**
     * @var OpinionRepository
     */
    private $opinionRepository;

    /**
     * @var ServiceRepository
     */
    private $serviceRepository;

    public function __construct(HabitatRepository $habitatRepository, OpinionRepository $opinionRepository, ServiceRepository $serviceRepository)
    {
        $this->habitatRepository = $habitatRepository;
        $this->opinionRepository = $opinionRepository;
        $this->serviceRepository = $serviceRepository;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'current_menu' => 'home',
            'habitats' => $this->habitatRepository->findLastThree(),
            'reviews' => $this->opinionRepository->findVisibleOpinion(),
            'services' => $this->serviceRepository->findLastThree()
        ]);
    }
}
