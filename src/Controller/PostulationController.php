<?php

namespace App\Controller;

use App\Entity\Postulation;
use App\Form\PosType;
use App\Entity\Users;
use App\service\Pdf;
use Dompdf\Dompdf;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Dompdf\Options;
use App\Repository\PostulationRepository;
use App\Repository\UsersRepository;
use App\Form\SearchType;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Cache\CacheItem;
use Psr\Cache\CacheItemInterface as ItemInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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
        $pos = $cache->get('postulation', function (ItemInterface $item) {
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
        return $this->render(
            'Admin/index.html.twig'

        );
    }

    #[Route('/addPos', name: 'postulation_add')]
    public function addPos(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $creator = $this->getUser();
        $postulation = new postulation();

        $form = $this->createForm(posType::class, $postulation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postulation->setCreator($creator);
            $em = $this->getDoctrine()->getManager();
            $em->persist($postulation); //add
            $em->flush();


            return $this->redirectToRoute('display_pos');
        }
        return $this->render('postulation/addPos.html.twig', ['form' => $form->createView()]);
    }
    #[Route('/removepos/{idPos}', name: 'remove_pos')]
    public function RemovePos(Postulation $postulation): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($postulation);
        $em->flush();
        return $this->redirectToRoute('display_pos');
    }
    #[Route('/updatePos/{idPos}', name: 'postulation_update')]
    public function UpdatePos(Request $request, $idPos): Response
    {

        $postulation = $this->getDoctrine()->getManager()->getRepository(Postulation::class)->find($idPos);

        $form = $this->createForm(posType::class, $postulation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            return $this->redirectToRoute('display_pos');
        }
        return $this->render('postulation/updatePos.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/Admin/postulation/total1', name: 'total_Postulations1')]
    public function getTotalPostulations1(PostulationRepository $PostulationRepository): Response
    {

        $totalPostulations = $PostulationRepository->countPostulations();
        $pos = $this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll();
        return $this->render('postulation/my_postulations.html.twig', [
            'totalPostulations' => $totalPostulations,
            'p' => $pos
        ]);
    }


    #[Route('/Admin/postulation/total2', name: 'total_Postulations2')]
    public function getTotalPostulations2(PostulationRepository $PostulationRepository): Response
    {

        $totalPostulations = $PostulationRepository->countPostulations();
        $pos = $this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll();
        return $this->render('postulation/dashboard.html.twig', [
            'totalPostulations' => $totalPostulations,
            'p' => $pos
        ]);
    }

    #[Route('/pdf', name: 'pdfpos')]
    public function pdfpos()
    {

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $pos = $this->getDoctrine()->getManager()->getRepository(Postulation::class)->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('postulation/pdf.html.twig', [
            'p' => $pos

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

    #[Route('/graph', name: 'app_homepage')]
    public function graph(ChartBuilderInterface $chartBuilder): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        return $this->render('postulation/stat.html.twig', [
            'chart' => $chart,
        ]);
    }
    public  function statistiques()
    {
        return $this->render('postulation/stat.html.twig');
    }
    #[Route('/email', name: 'email')]
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        // Create a FormView object from the Form object
        $formView = $form->createView();
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $adresse = $data['email'];
            $content = $data['content'];
            $email = (new Email())
                ->from($adresse)
                ->to('admin@admin.com')
                ->subject('demande de contact')
                ->text($content);
            $mailer->send($email);
        }



        return $this->renderForm('postulation/mail.html.twig', [
            'formulaire' => $form,
        ]);
    }
}
