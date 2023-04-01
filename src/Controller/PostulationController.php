<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostulationController extends AbstractController
{
    #[Route('/postulation', name: 'app_postulation')]
    public function index(): Response
    {
        return $this->render('postulation/index.html.twig', [
            'controller_name' => 'PostulationController',
        ]);
    }
    
    #[Route('/postulation/add', name: 'postulation_add')]
    public function addPos(ManagerRegistry $doctrine,Request $req): Response {
        $postulation = new postulation();
     $form=$this->createForm(posType::class,$postulation);
     return $this->render('postulation/index.html.twig', [
        'controller_name' => 'PostulationController',
    ]);
    }


    
}
