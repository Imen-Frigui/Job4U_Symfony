<?php

namespace App\service;
use Dompdf\Dompdf;
use Dompdf\Options;
class pdfService
{


private $dompdf;
public function __construct(){
   $dompdf = new DomPdf();



    $pdfOptions=new Options();
    $pdfOptions->set ('defaultFont','Garamond');
    $domPdf->setOptions($pdfOptions);

}


public function showPdfFile($html){
    $this->domPdf->loadHtml($html);
    $this->domPdf->render();
    $this->domPdf->stream('details.pdf',['attachement'=>false]);

}
public function generateBinaryPDF($html){
    $this->domPdf->loadHtml($html);
    $this->domPdf->render();
    $this->domPdf->output();




}

}

