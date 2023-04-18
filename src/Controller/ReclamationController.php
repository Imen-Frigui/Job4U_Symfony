<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\QrCodeService;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\NotoSans;
use Symfony\Component\HttpFoundation\JsonResponse;
#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }
    #[Route('/reclamationsFront', name: 'reclamationsFront', methods: ['GET'])]
    public function reclamationsFront(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/listeReclamations.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

    #[Route('/stat', name: 'stat', methods: ['GET'])]
    public function stat(ReclamationRepository $reclamationRepository,EntityManagerInterface $entityManager): Response
    {
        // Get the current date
$currentDate = new \DateTime();

// Create a DQL query
$dqlDailyResponsesCount = "SELECT COUNT(rp) AS responsesCount
        FROM App\Entity\Reponse rp
        WHERE rp.idReclamation IN (
            SELECT r.idReclamation
            FROM App\Entity\Reclamation r
            WHERE r.statut = :statut
        ) AND rp.dateRep = :currentDate";
        // Execute the DQL query
$query = $entityManager->createQuery($dqlDailyResponsesCount)
->setParameter('currentDate', $currentDate->format('Y-m-d'))
->setParameter('statut', 'Traité');

// Get the results
$results = $query->getResult();
$responseDailyTraite=$results[0]['responsesCount'];

$dqlDailyResponsesTypeCount = "SELECT COUNT(rp) AS responsesCount
FROM App\Entity\Reponse rp
WHERE rp.idReclamation IN (
    SELECT r.idReclamation
    FROM App\Entity\Reclamation r
    WHERE r.type = :type
) AND rp.dateRep = :currentDate";
$query = $entityManager->createQuery($dqlDailyResponsesTypeCount)
->setParameter('currentDate', $currentDate->format('Y-m-d'))
->setParameter('type', 'Help');
$results = $query->getResult();
$responseTypeTraite=$results[0]['responsesCount'];


$dqlResponsesCount = "SELECT COUNT(rp) AS responsesCount
FROM App\Entity\Reponse rp
WHERE rp.idReclamation IN (
    SELECT r.idReclamation
    FROM App\Entity\Reclamation r
    WHERE r.statut = :statut
) ";
$query = $entityManager->createQuery($dqlResponsesCount)
->setParameter('statut', 'Traité');
$results = $query->getResult();
$reponseStatut=$results[0]['responsesCount'];

        return $this->render('reclamation/dashboard.html.twig', [
            'responseDailyTraite' => $responseDailyTraite,
            'responseTypeTraite' => $responseTypeTraite,
            'reponseStatut' => $reponseStatut,

        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setStatut('Non Traité');
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('reclamationsFront', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{idReclamation}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/front/recSearch', name: 'recSearch', methods: ['GET','POST'])]
    public function search(Request $request, EntityManagerInterface $entityManager)
    {
        // Get search parameters from request
        $searchType = $request->query->get('searchType');
        $searchValue = $request->query->get('searchValue');
        
        // Query the database with search parameters using DQL
        $query = $entityManager->createQuery("SELECT t FROM App\Entity\Reclamation t WHERE t.$searchType LIKE :searchValue")
            ->setParameter('searchValue', '%' . $searchValue . '%');
        $reclamations = $query->getResult();
       
         // Manually serialize entities to avoid circular references
         $serializedRecs = [];
         foreach ($reclamations as $reclamation) {
             $serializedRecs[] = [
                
                'idReclamation' => $reclamation->getIdReclamation(),
                 'message' => $reclamation->getMessage(),
                 'type' => $reclamation->getType(),
                 'statut' => $reclamation->getStatut(),
                 
             ];
         }
            // Create JSON response
        $response = new JsonResponse();
        $response->setData(['reclamations' => $serializedRecs]);
        return $response;
    }

    #[Route('/showRecFront/{idReclamation}', name: 'showRecFront', methods: ['GET'])]
    public function showRecFront(Reclamation $reclamation): Response
    {
        $writer = new PngWriter();
        $qrCode = QrCode::create('Message  : '. $reclamation->getMessage(). ' Type : ' .$reclamation->getType() . ' Statut : ' . $reclamation->getStatut()   )
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(120)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $logo = null;
        $label = Label::create('')->setFont(new NotoSans(8));
 
        $qrCodes = [];
        $qrCodes['img'] = $writer->write($qrCode, $logo)->getDataUri();
        $qrCodes['simple'] = $writer->write(
                                $qrCode,
                                null,
                                $label->setText('Reclamation')
                            )->getDataUri();
 
        $qrCode->setForegroundColor(new Color(255, 0, 0));
        $qrCode->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 0, 0));
 
        $qrCode->setSize(200)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
        $qrCodes['withImage'] = $writer->write(
            $qrCode,
            null,
            $label->setText('With Image')->setFont(new NotoSans(10))
        )->getDataUri();
        return $this->render('reclamation/showFront.html.twig', [
            'reclamation' => $reclamation,
            'qrCodes'=>$qrCodes,

        ]);
    }


    #[Route('/{idReclamation}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/front/{idReclamation}/edit', name: 'app_reclamationFront_edit', methods: ['GET', 'POST'])]
    public function editFront(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('reclamationsFront', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/editFront.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{idReclamation}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getIdReclamation(), $request->request->get('_token'))) {
            $reclamationRepository->remove($reclamation, true);
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}
