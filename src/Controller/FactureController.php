<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FactureController extends AbstractController
{
    #[Route('/admin/commande/{id}/facture', name: 'app_facture')]
    public function index($id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);
     
       // Génération du PDF ici...
       $pdfOptions = new Options();
       $pdfOptions->set('defaultFont', 'Arial');
       $pdfOptions->setIsRemoteEnabled(true); // Nouvelle conf
       $pdfOptions->set('isHtml5ParserEnabled', true); // Nouvelle conf
       $domPdf = new Dompdf($pdfOptions);

       // Récupère et converti l'image en base64
       $pathToImage = $this->getParameter('kernel.project_dir') . '/public/uploads/images/WPreParfumerLogo.jpg';
       $rawLogo = file_get_contents($pathToImage);
       $rawMime = mime_content_type($pathToImage);
       $logo = "data:{$rawMime};base64," . base64_encode($rawLogo);

       // Appel du template
       $html = $this->renderView('facture/index.html.twig', [
           'commande' => $commande,
           'logo' => $logo
       ]);

       $domPdf->loadHtml($html);
       $domPdf->setPaper('A4', 'portrait');
       $domPdf->render();
       $output = $domPdf->output();

       return new Response($output, 200, [
           'Content-Type' => 'application/pdf',
       ]);
    }
    
}
