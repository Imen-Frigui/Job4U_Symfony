<?php

namespace App\Controller;
use App\Entity\Societe;
use App\Form\SosType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
class SocieteController extends AbstractController
{
    #[Route('/', name: 'display_sos')]
    public function index(): Response
    {  
        $pos=$this->getDoctrine()->getManager()->getRepository(Societe::class)->findAll(); 
        return $this->render('societe/index.html.twig', [
           's'=>$sos,
        ]);
    }
    #[Route('/admin', name: 'display_admin')]
    public function indexAdmin(): Response
    {  
        return $this->render('Admin/index.html.twig'

        );
    }
    
    #[Route('/addSos', name: 'Societe_add')]
    public function addSos(Request $request): Response{
    $entityManager = $this->getDoctrine()->getManager();
    $Societe= new societe(); 
    
    $form=$this->createForm(sosType::class,$Societe);
    $form->handleRequest($request);
     if ($form->isSubmitted()&& $form->isValid()){
        $em= $this->getDoctrine()->getManager();
        $em->persist($Societe);//add
        $em->flush();
        

        return $this->redirectToRoute('display_sos');
     }
     return $this->render('societe/createSos.html.twig',['form'=>$form->createView()]);

    }  
    #[Route('/removesos/{id}', name: 'remove_sos')]
    public function RemoveSos(Societe $societe): Response
    {  
       $em=$this->getDoctrine()->getManager();
       $em->remove($societe);
       $em->flush();
       return $this->redirectToRoute('display_sos');
    }
    #[Route('/updateSos/{id}', name: 'societe_update')]
    public function UpdateSos(Request $request,$id): Response{
    
    $societe = $this->getDoctrine()->getManager()->getRepository(Societe::class)->find($id); 
    
    $form=$this->createForm(sosType::class,$societe);
    $form->handleRequest($request);
     if ($form->isSubmitted()&& $form->isValid()){
        $em= $this->getDoctrine()->getManager();
      
        $em->flush();

        return $this->redirectToRoute('display_sos');
     }
     return $this->render('societe/updateSos.html.twig',['form'=>$form->createView()]);

    }  
}
