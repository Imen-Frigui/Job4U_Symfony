<?php

namespace App\Controller;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use App\Entity\Offre;
use App\Entity\User;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use App\Entity\PropertySearch;
use App\Form\OffreType;
use App\Form\PropertySearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\OffreRepository;
use App\Repository\CategoriesRepository;
use App\Service\PdfService;
use Knp\Component\Pager\PaginatorInterface;


class OffreController extends AbstractController
{
    #[Route('/admin', name: 'display_offre')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $offres=$doctrine
        ->getRepository(offre::class)->findAll();
        return $this->render('offre/affOffre.html.twig',
         ['of'=>$offres
            
        ]);
        #return $this->render('offre/index.html.twig', [
       #     'controller_name' => 'OffreController',
     #   ]);
    }

    #[Route('/useroffre', name: 'user_offre')]
    public function index1(OffreRepository $r,PaginatorInterface $paginator,Request $request): Response
    {
      
        $queryBuilder = $r->createQueryBuilder('p')
        ->orderBy('p.id', 'DESC')
        ->getQuery();
    
    $offres = $paginator->paginate(
        $queryBuilder,
        $request->query->getInt('page', 1),
        6
    );
        return $this->render('user/index.html.twig',
         ['offres'=>$offres
            
        ]);

    }

    #[Route('/simpleoffre', name: 'simple_offre')]
    public function simple(OffreRepository $r,PaginatorInterface $paginator,Request $request): Response
    {
      
        $queryBuilder = $r->createQueryBuilder('p')
        ->orderBy('p.id', 'DESC')
        ->getQuery();
    
    $offres = $paginator->paginate(
        $queryBuilder,
        $request->query->getInt('page', 1),
        6
    );
        return $this->render('simpleuser/offers.html.twig',
         ['offres'=>$offres
            
        ]);

    }


    #[Route('/offre2/{id}', name: 'display_offr2')]
    public function indexv2($id,ManagerRegistry $doctrine,OffreRepository $r): Response
    {
        $offres = $doctrine->getRepository(Offre::class)->findBy(['id' => $id]);
        $offre=$r->find($id);
        return $this->render('user/myoffre.html.twig',
         ['offy'=>$offres    
        ]);
        #return $this->render('offre/index.html.twig', [
       #     'controller_name' => 'OffreController',
     #   ]
        }

        #[Route('/offre2', name: 'display_offre2')]
        public function indexy2(ManagerRegistry $doctrine): Response
        {
            $offres=$doctrine
            ->getRepository(offre::class)->findAll();
            return $this->render('offre/affOffre2.html.twig',
             ['c'=>$offres
                
            ]);}

        #[Route('/mesoffres', name: 'mesoffers')]
    public function mesOffers(ManagerRegistry $doctrine,OffreRepository $r,PaginatorInterface $paginator,Request $request): Response
    {
        //  $user = $em->getRepository(User::class)->find(1);
        $queryBuilder = $r->createQueryBuilder('p')
        
   ->where('p.creator = 1')
   ->orderBy('p.id', 'ASC')
        ->getQuery();
    
    $offres = $paginator->paginate(
        $queryBuilder,
        $request->query->getInt('page', 1),
        6
    );
        return $this->render('user/mesoffres.html.twig',
         ['mesoffers'=>$offres
            
        ]);

        
    }


     #[Route('/offresearch', name: 'search')]
    public function indexv1(ManagerRegistry $doctrine,Request $request): Response
    {

        
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class,$propertySearch);
        $form->handleRequest($request);
       //initialement le tableau des articles est vide, 
       //c.a.d on affiche les articles que lorsque l'utilisateur clique sur le bouton rechercher
        $offres= [];

        if($form->isSubmitted() && $form->isValid()) {
            //on récupère le nom d'article tapé dans le formulaire
             $nom = $propertySearch->getNom();   
             if ($nom!="") 
               //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
               $offres=$doctrine->getRepository(Offre::class)->findBy(['nom' => $nom] );
             else   
               //si si aucun nom n'est fourni on affiche tous les articles
               $offres=$doctrine->getRepository(Offre::class)->findAll();
            }
             return  $this->render('user/form.html.twig',[ 'form' =>$form->createView(), 'offres' => $offres]);  
           }

           #[Route('/simplesearch', name: 'ssearch')]
    public function ssearch(ManagerRegistry $doctrine,Request $request): Response
    {

        
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class,$propertySearch);
        $form->handleRequest($request);
       //initialement le tableau des articles est vide, 
       //c.a.d on affiche les articles que lorsque l'utilisateur clique sur le bouton rechercher
        $offres= [];

        if($form->isSubmitted() && $form->isValid()) {
            //on récupère le nom d'article tapé dans le formulaire
             $nom = $propertySearch->getNom();   
             if ($nom!="") 
               //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
               $offres=$doctrine->getRepository(Offre::class)->findBy(['nom' => $nom] );
             else   
               //si si aucun nom n'est fourni on affiche tous les articles
               $offres=$doctrine->getRepository(Offre::class)->findAll();
            }
             return  $this->render('simpleuser/searOf.html.twig',[ 'form' =>$form->createView(), 'offres' => $offres]);  
           }



           #[Route('/adminsearch', name: 'adminsearch')]
           public function search(ManagerRegistry $doctrine,Request $request): Response
           {
       
               
               $propertySearch = new PropertySearch();
               $form = $this->createForm(PropertySearchType::class,$propertySearch);
               $form->handleRequest($request);
              //initialement le tableau des articles est vide, 
              //c.a.d on affiche les articles que lorsque l'utilisateur clique sur le bouton rechercher
               $offres= [];
       
               if($form->isSubmitted() && $form->isValid()) {
                   //on récupère le nom d'article tapé dans le formulaire
                    $nom = $propertySearch->getNom();   
                    if ($nom!="") 
                      //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
                      $offres=$doctrine->getRepository(Offre::class)->findBy(['nom' => $nom] );
                    else   
                      //si si aucun nom n'est fourni on affiche tous les articles
                      $offres=$doctrine->getRepository(Offre::class)->findAll();
                   }
                    return  $this->render('offre/affOffre.html.twig',[ 'form' =>$form->createView(), 'of' => $offres]);  
                  }



           #[Route('/addOffre', name: 'addOffre')]
           public function addOffre(ManagerRegistry $doctrine,Request $request)
            {
           $offre = new offre();
           $creator = $doctrine->getRepository(User::class)->find(1);
           $form = $this->createForm(OffreType::class, $offre);
           $form->handleRequest($request);
           if ($form ->isSubmitted() && $form->isValid()){
               $offre->setCreator($creator);
               $em = $doctrine->getManager();
               $em->persist($offre);//add
               $em->flush();
              # $this->addFlash('notice', 'Hello world');
           return $this->redirectToRoute("mesoffers");
           }
            return $this->render('offre/addOffre.html.twig', [
               'f' => $form->createView(),
                ]);
               }



    #[Route('/', name: 'display_admin')]
    public function indexAdmin(ManagerRegistry $doctrine): Response
    {
        return $this->render('admin/index.html.twig');
        #return $this->render('offre/index.html.twig', [
       #     'controller_name' => 'OffreController',
     #   ]);
    }

    #[Route('/pdf/{id}',name:'pdfO')]
    public function pdfpos($id,OffreRepository $r,PdfService $pdf,ManagerRegistry $doctrine)
    {
        $of=$doctrine->getRepository(Offre::class)->findBy(['id' => $id] );
        $html = $this->render('user/form.html.twig',
        ['offres'=>$of]);
        $pdf->showPdfFile($html);
        $transport = Transport::fromDsn('smtp://touati.ahmed@esprit.tn:ihhtpuvuwpdknuik@smtp.gmail.com:587');
        $off=$doctrine->getRepository(Offre::class)->findBy(['id' => $id] );

// Create a Mailer object
$mailer = new Mailer($transport);
                                $email = (new Email())
                                ->from('touati-ahmed@esprit.tn')
                                    ->to('spnahmed1@gmail.com')
                                    //->cc('cc@example.com')
                                    //->bcc('bcc@example.com')
                                    //->replyTo('fabien@example.com')
                                    //->priority(Email::PRIORITY_HIGH)
                                    ->subject('Offre Printed!')
                                    //->text('Text',$nom,'lol',$description,'duree',$duree)
                                    ->text('The plain text version of the message.')
                                    ->html('<p>Sir your Offre Has been printed and you can share it !</p>');
                                    //->attachFromPath('assets/offre.pdf');
                                   // ->addPart(new DataPart(new File('assets/offre.pdf')));
                        
                                $mailer->send($email);
                        
                                // ...
                                return $this->redirectToRoute("user_offre");
                                
                          


    }


    #[Route('/suppOffre/{id}', name: 'suppO')]
    public function suppOffre($id,OffreRepository $r,
    ManagerRegistry $doctrine): Response
    { //récupérer la classroom à supprimer
    $offre=$r->find($id);
    //Action suppression
     $em =$doctrine->getManager();
     $em->remove($offre);
     $em->flush();
return $this->redirectToRoute('display_offre');
}

#[Route('/updateO/{id}', name: 'updateOffre')]
        public function modifierOffre(ManagerRegistry $doctrine,Request $request,$id,OffreRepository $r)
                               {
              { //récupérer la classroom à modifier
                $offre=$r->find($id);
            $form=$this->createForm(OffreType::class,$offre);
             $form->handleRequest($request);
             if($form ->isSubmitted() && $form->isValid()){
            $em =$doctrine->getManager() ;
                 $em->flush();
             return $this->redirectToRoute("display_offre");}
             return $this->renderForm("offre/addOffre.html.twig",
      array("f"=>$form));
                                }
                            }

                            #[Route('/email/{id}', name: 'sendEmail')]
                            public function sendEmail($id,ManagerRegistry $doctrine): Response
                            {
                              // Create a Transport object
$transport = Transport::fromDsn('smtp://touati.ahmed@esprit.tn:ihhtpuvuwpdknuik@smtp.gmail.com:587');
$off=$doctrine->getRepository(Offre::class)->findBy(['id' => $id] );

// Create a Mailer object
$mailer = new Mailer($transport);
                                $email = (new Email())
                                ->from('touati-ahmed@esprit.tn')
                                    ->to('spnahmed1@gmail.com')
                                    //->cc('cc@example.com')
                                    //->bcc('bcc@example.com')
                                    //->replyTo('fabien@example.com')
                                    //->priority(Email::PRIORITY_HIGH)
                                    ->subject('Offre Printed!')
                                    //->text('Text',$nom,'lol',$description,'duree',$duree)
                                    ->text('The plain text version of the message.')
                                    ->html('<p>See your Offre on Web !</p>')
                                    ->attachFromPath('assets/offre.pdf');
                        
                                $mailer->send($email);
                        
                                // ...
                                return $this->redirectToRoute("user_offre");
                                
                            }


#[Route('/stats', name: 'stats')]
    public function statistique(CategoriesRepository $cat)
    {
        $categories = $cat->findAll();

        $categNom=[];
        $categCount=[];
        foreach($categories as $categorie){
            $categNom[] = $categorie->getNom();
            $categCount[] = count($categorie->getCategories());
        }
        return $this->render('admin/stats.html.twig', [
            'categNom' => json_encode($categNom),
            'categCount' => json_encode($categCount),]);
    
}
}
