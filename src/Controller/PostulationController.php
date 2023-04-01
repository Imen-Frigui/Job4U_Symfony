<?php

namespace App\Controller;
use App\Entity\Postulation;
use App\Form\PosType;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostulationController extends AbstractController
{
    #[Route('/', name: 'display_pos')]
    public function index(): Response
    {  
        $pos=$this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll(); 
        return $this->render('postulation/index.html.twig', [
           'p'=>$pos,
        ]);
    }
    #[Route('/admin', name: 'display_admin')]
    public function indexAdmin(): Response
    {  
        return $this->render('Admin/index.html.twig'

        );
    }
    
    #[Route('/addPos', name: 'postulation_add')]
    public function addPos(Request $request): Response{
    $entityManager = $this->getDoctrine()->getManager();
    $postulation = new postulation(); 
    
    $form=$this->createForm(posType::class,$postulation);
    $form->handleRequest($request);
     if ($form->isSubmitted()&& $form->isValid()){
        $em= $this->getDoctrine()->getManager();
        $em->persist($postulation);//add
        $em->flush();
        $nextAction = $form->get('ajouter')->isClicked()
        ? 'task_new'
        : 'task_success';

        return $this->redirectToRoute('display_pos');
     }
     return $this->render('postulation/createPos.html.twig',['form'=>$form->createView()]);

    }  
    #[Route('/removepos/{idPos}', name: 'remove_pos')]
    public function RemovePos(Postulation $postulation): Response
    {  
       $em=$this->getDoctrine()->getManager();
       $em->remove($postulation);
       $em->flush();
       return $this->redirectToRoute('display_pos');
    }
    #[Route('/updatePos/{idPos}', name: 'postulation_update')]
    public function UpdatePos(Request $request,$idPos): Response{
    
    $postulation = $this->getDoctrine()->getManager()->getRepository(Postulation::class)->find($idPos); 
    
    $form=$this->createForm(posType::class,$postulation);
    $form->handleRequest($request);
     if ($form->isSubmitted()&& $form->isValid()){
        $em= $this->getDoctrine()->getManager();
      
        $em->flush();

        return $this->redirectToRoute('display_pos');
     }
     return $this->render('postulation/updatePos.html.twig',['form'=>$form->createView()]);

    }  
}
