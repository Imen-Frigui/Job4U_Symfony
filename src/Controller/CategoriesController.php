<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Form\CategoriesType;

class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'display_Cat')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories=$doctrine->getRepository(Categories::class)->findAll();
        return $this->render('categories/affCat.html.twig',
         ['cat'=>$categories
            
        ]);
    }


    #[Route('/addCat', name: 'addCat')]
    public function addOffre(ManagerRegistry $doctrine,Request $request)
     {
    $categories = new Categories();
    $form = $this->createForm(CategoriesType::class, $categories);
    $form->handleRequest($request);
    if ($form ->isSubmitted() && $form->isValid()){
        $em = $doctrine->getManager();
        $em->persist($categories);//add
        $em->flush();
       # $this->addFlash('notice', 'Hello world');
    return $this->redirectToRoute("display_Cat");
    }
     return $this->render('categories/addCat.html.twig', [
        'f' => $form->createView(),
         ]);
        }


    #[Route('/suppCat/{id}', name: 'suppCat')]
    public function suppCat($id,CategoriesRepository $r,
    ManagerRegistry $doctrine): Response
    { //récupérer la classroom à supprimer
    $categorie=$r->find($id);
    //Action suppression
     $em =$doctrine->getManager();
     $em->remove($categorie);
     $em->flush();
return $this->redirectToRoute('display_Cat');
}

#[Route('/updatecat/{id}', name: 'updateCat')]
        public function modifierOffre(ManagerRegistry $doctrine,Request $request,$id,CategoriesRepository $r)
                               {
              { //récupérer la classroom à modifier
                $offre=$r->find($id);
            $form=$this->createForm(CategoriesType::class,$offre);
             $form->handleRequest($request);
             if($form ->isSubmitted() && $form->isValid()){
            $em =$doctrine->getManager() ;
                 $em->flush();
             return $this->redirectToRoute("display_Cat");}
             return $this->renderForm("categories/addCat.html.twig",
      array("f"=>$form));
                                }
                            }


}
