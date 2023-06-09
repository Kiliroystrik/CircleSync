<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Service\GroupManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GroupRepository $groupRepository): Response
    {
        $groupRepository->findAll();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'groups' => $groupRepository->findAll(),
        ]);
    }
}
