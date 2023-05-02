<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use Dompdf\Dompdf;
use Dompdf\Options;
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
use Twilio\Rest\Client;

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
        WHERE rp.id IN (
            SELECT r.id
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
WHERE rp.id IN (
    SELECT r.id
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
WHERE rp.id IN (
    SELECT r.id
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
           
            $mail = new PHPMailer(true);
            $mail->isSMTP();// Set mailer to use SMTP
            $mail->CharSet = "utf-8";// set charset to utf8
            $mail->SMTPAuth = true;// Enable SMTP authentication
            $mail->SMTPSecure = 'tls';// Enable TLS encryption, ssl also accepted
            $mail->Host = 'smtp.gmail.com';// Specify main and backup SMTP servers
            $mail->Port = 587;// TCP port to connect to
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->isHTML(true);// Set email format to HTML
            $mail->Username = 'dridi.mariem@esprit.tn ';// SMTP username
            $mail->Password = 'ESENESPRIT223JFT2123';// SMTP password
            $mail->setFrom('dridi.mariem@esprit.tn', 'mariem dridi');//Your application NAME and EMAIL
            $mail->Subject = 'reclamation bien créer';//Message subject
            $mail->Body = '<h1>Bonjour Monsieur/Madame <br> 

            votre reclamation a ete cree  ' . $reclamation->getMessage(). ' Merci pour votre confiance </h1>';

            $mail->addAddress('dridimaryem124@gmail.com', 'dridimaryem');// Target email
            $mail->send();
            $reclamation->setMessage($this->filterwords($reclamation->getMessage()));

            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('reclamationsFront', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/reclamation/pdf/', name: 'pdf0', methods: ['GET'])]
    public function Pdf(ReclamationRepository $ReclamationRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $l = $ReclamationRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reclamation/pdf.html.twig', [
            'reclamations' =>$l,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);
        return new Response();
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
                
                'id' => $reclamation->getId(),
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

    #[Route('/showRecFront/{id}', name: 'showRecFront', methods: ['GET'])]
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


    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             // Your Account SID and Auth Token from twilio.com/console
             $sid = 'AC8f656b6aab0162ee8be3d35737158197';
             $auth_token = 'd3bcc471da64c1d230dcecd5ed8c90fa';
             // In production, these should be environment variables. E.g.:
             // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]
             // A Twilio number you own with SMS capabilities
             $twilio_number = "+15674074471";

             $client = new Client($sid, $auth_token);
             $client->messages->create(
             // the number you'd like to send the message to
                 '+21622800427',
                 [
                     // A Twilio phone number you purchased at twilio.com/console
                     'from' => '+15674074471',
                     // the body of the text message you'd like to send
                     'body' => 'votre reclamation a été traité merci de nous contacter pour plus de détail!'
                 ]
             );
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/front/{id}/edit', name: 'app_reclamationFront_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $reclamationRepository->remove($reclamation, true);
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


    public function filterwords($text)
    {
        $filterWords = array( 'Bhimm', 'Msatek',  'Shit life');
        $filterCount = count($filterWords);
        $str = "";
        $data = preg_split('/\s+/',  $text);
        foreach($data as $s){
            $g = false;
            foreach ($filterWords as $lib) {
                if($s == $lib){
                    $t = "";
                    for($i =0; $i<strlen($s); $i++) $t .= "*";
                    $str .= $t . " ";
                    $g = true;
                    break;
                }
            }
            if(!$g)
                $str .= $s . " ";
        }
        return $str;
    }
}
