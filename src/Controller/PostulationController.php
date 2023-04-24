<?php

namespace App\Controller;
use App\Entity\Postulation;
use App\Form\PosType;
use App\Entity\Users;
use App\service\pdfService;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\PostulationRepository;
use App\Repository\UsersRepository;
use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
 use Symfony\Component\Cache\CacheItem;
 use Psr\Cache\CacheItemInterface as ItemInterface;
class PostulationController extends AbstractController
{
    #[Route('/', name: 'display_pos')]
    public function index(Request $request, CacheInterface $cache): Response
    {
    //$texte = $cache->get('texte_detailss', function (ItemInterface $item) {
       // $item->expiresAfter(20);
       // return $this->fonctionLongue();
   // });

    //$pos = $this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll();
    $pos=$cache->get('postulation',function(ItemInterface $item){
        $item->expiresAfter(20);
        return $pos = $this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll();
    });
    return $this->render('postulation/index.html.twig', [
        'p' => $pos,
    ]);
}
    #[Route('/pos', name: 'display')]
    public function displayPostulations(Request $request, PaginatorInterface $paginator, PostulationRepository $postulationRepository): Response
    {  
    $queryBuilder = $postulationRepository->createQueryBuilder('p')
        ->orderBy('p.idPos', 'DESC')
        ->getQuery();
    
    $postulations = $paginator->paginate(
        $queryBuilder,
        $request->query->getInt('page', 1),
        6
    );
    
    return $this->render('postulation/index2.html.twig', [
       'postulations' => $postulations,
       
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
    $creator = $entityManager->getRepository(Users::class)->find(1);
    $postulation = new postulation(); 
    
    $form=$this->createForm(posType::class,$postulation);
    $form->handleRequest($request);
     if ($form->isSubmitted()&& $form->isValid()){
        $postulation->setCreator($creator);
        $em= $this->getDoctrine()->getManager();
        $em->persist($postulation);//add
        $em->flush();
        

        return $this->redirectToRoute('display_pos');
     }
     return $this->render('postulation/addPos.html.twig',['form'=>$form->createView()]);

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
    
    #[Route('/Admin/postulation/total1', name: 'total_Postulations1')]
    public function getTotalPostulations1(PostulationRepository $PostulationRepository): Response
    {
       
        $totalPostulations = $PostulationRepository->countPostulations();
        $pos=$this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll();
        return $this->render('postulation/my_postulations.html.twig', [
            'totalPostulations' => $totalPostulations,
            'p'=>$pos
        ]);
    }


    #[Route('/Admin/postulation/total2', name: 'total_Postulations2')]
    public function getTotalPostulations2(PostulationRepository $PostulationRepository): Response
    {
       
        $totalPostulations = $PostulationRepository->countPostulations();
        $pos=$this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll();
        return $this->render('postulation/showPos.html.twig', [
            'totalPostulations' => $totalPostulations,
            'p'=>$pos
        ]);


    }

#[Route('/pdf',name:'pdfpos')]
public function pdfpos()
{
    
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');

    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);
    
    $pos=$this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll();

    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('postulation/pdf.html.twig', [   
        'p'=>$pos
       
    ]);

    // Load HTML to Dompdf
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser (force download)
    $dompdf->stream("mypdf.pdf", [
        "Attachment" => false
    ]);
}

//#[Route('/search', name: 'search')]
//public function search2(Request $request): Response
//{
   // $form = $this->createForm(SearchType::class);
    //$form->handleRequest($request);
//
    //if ($form->isSubmitted() && $form->isValid()) {
       // $data = $form->getData();
       // $email = $form->get('email')->getData();
       // $creator = $data['creator'];
//
        //$entityManager = $this->getDoctrine()->getManager();
        //$postulationRepository = $entityManager->getRepository(Postulation::class);

        //if ($email!== null) {
          //  $postulations = $postulationRepository->findBy(['email' => $email]);
       // } elseif ($creator !== null) {
          //  $user = $entityManager->getRepository(Users::class)->findOneBy(['nom' => $creator]);
           // if (!$user) {
           //     throw $this->createNotFoundException('No user found for username '.$creator);
          //  }
           // $postulations = $postulationRepository->findBy(['creator' => $user]);
       // } else {
          //  $postulations = $postulationRepository->findAll();
       // }

       // return $this->render('postulation/searchResult.html.twig', [
          //  'postulations' => $postulations,
          //  'form' => $form->createView(),
        //]);
   // }

    //return $this->render('postulation/searchdata.html.twig', [
       // 'form' => $form->createView(),
   // ]);

   #[Route('/search', name: 'search')]
public function search(Request $request): Response
{
    $form = $this->createForm(SearchType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $Nom = $data['name'];

        $entityManager = $this->getDoctrine()->getManager();
        $UsersRepository = $entityManager->getRepository(Users::class);
        $user = $UsersRepository->findOneBy(['name' => $Nom]);

        if (!$user) {
            throw $this->createNotFoundException('No user found for name '.$Nom);
        }

        $postulationRepository = $entityManager->getRepository(Postulation::class);
        $postulations = $postulationRepository->findBy(['creator' => $user]);

        return $this->render('postulation/searchResult.html.twig', [
            'postulations' => $postulations,
            'form' => $form->createView(),
        ]);
    }

    return $this->render('postulation/searchdata.html.twig', [
        'form' => $form->createView(),
    ]);
}
private function fonctionLongue(){
    sleep(3);
  return  'brouette';
}

   
  

}