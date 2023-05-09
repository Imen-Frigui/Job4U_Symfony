<?php

namespace App\Controller\Mobile;

use App\Entity\Report;
use App\Repository\ReportRepository;
use App\Repository\UserRepository;
use App\Repository\PosteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mobile/report")
 */
class ReportMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(ReportRepository $reportRepository): Response
    {
        $reports = $reportRepository->findAll();

        if ($reports) {
            return new JsonResponse($reports, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, UserRepository $userRepository, PosteRepository $posteRepository): JsonResponse
    {
        $report = new Report();


        $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }

        $poste = $posteRepository->find((int)$request->get("poste"));
        if (!$poste) {
            return new JsonResponse("poste with id " . (int)$request->get("poste") . " does not exist", 203);
        }


        $report->constructor(
            $user,
            $poste,
            $request->get("type"),
            $request->get("description")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($report);
        $entityManager->flush();


        $email = $user->getEmail();

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $transport = new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
                $transport->setUsername('app.esprit.pidev@gmail.com')->setPassword('dqwqkdeyeffjnyif');
                $mailer = new Swift_Mailer($transport);
                $message = new Swift_Message('Notification');
                $message->setFrom(array('app.esprit.pidev@gmail.com' => 'Notification'))
                    ->setTo(array($email))
                    ->setBody("<h1>Bienvenu a notre application</h1>", 'text/html');
                $mailer->send($message);
            } catch (Exception $exception) {
                return new JsonResponse(null, 405);
            }
        }

        return new JsonResponse($report, 200);


    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, ReportRepository $reportRepository, UserRepository $userRepository, PosteRepository $posteRepository): Response
    {
        $report = $reportRepository->find((int)$request->get("id"));

        if (!$report) {
            return new JsonResponse(null, 404);
        }


        $user = $userRepository->find((int)$request->get("user"));
        if (!$user) {
            return new JsonResponse("user with id " . (int)$request->get("user") . " does not exist", 203);
        }

        $poste = $posteRepository->find((int)$request->get("poste"));
        if (!$poste) {
            return new JsonResponse("poste with id " . (int)$request->get("poste") . " does not exist", 203);
        }


        $report->constructor(
            $user,
            $poste,
            $request->get("type"),
            $request->get("description")
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($report);
        $entityManager->flush();

        return new JsonResponse($report, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, ReportRepository $reportRepository): JsonResponse
    {
        $report = $reportRepository->find((int)$request->get("id"));

        if (!$report) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($report);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }


}
