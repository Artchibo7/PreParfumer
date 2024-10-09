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
        $domPdf = new Dompdf($pdfOptions);

        $html = $this->renderView('facture/index.html.twig', [
            'commande' => $commande,
        ]);

        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream("PreParfumer-facture" . $commande->getId() . ".pdf", [
            "Attachment" => false
        ]);

        return new Response(null, 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
