<?php

namespace App\Controller;
use App\Entity\Postulation;
use App\Form\PosType;
use App\Entity\Users;
use App\service\pdfService;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\PostulationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
class PostulationController extends AbstractController
{
    #[Route('/', name: 'display_pos')]
    public function index(Request $request): Response
    {  
       
        $pos=$this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll(); 
        return $this->render('postulation/index.html.twig', [
           'p'=>$pos,
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


  
}
