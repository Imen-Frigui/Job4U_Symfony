<?php

namespace App\Controller;

use App\Form\QRType;
use App\service\QrcodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class qrController extends AbstractController
{
    /**
     
     * @param Request $request
     * @param QrcodeService $qrcodeService
     * @return Response
     */
    #[Route('/QR', name: 'QR')]
    public function index(Request $request, QrcodeService $qrcodeService): Response
    {
        $qrCode = null;
        $form = $this->createForm(QRType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $qrCode = $qrcodeService->qrcode($data['name']);
        }

        return $this->render('default/QR.html.twig', [
            'form' => $form->createView(),
            'qrCode' => $qrCode
        ]);
    }
    
}
