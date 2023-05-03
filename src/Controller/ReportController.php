<?php

use App\Entity\Poste;
use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\PosteRepository;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    #[Route('/admin/report', name: 'admin_poste_report')]
    public function adminIndex(ReportRepository $reportRepository): Response
    {
        $reports = $reportRepository->findAll();

        return $this->render('report/admin_index.html.twig', [
            'reports' => $reports,
        ]);
    }
}
