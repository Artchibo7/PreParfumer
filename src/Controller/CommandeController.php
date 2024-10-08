<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Ville;
use App\Form\CommandeType;
use App\Repository\ProduitRepository;
use App\Service\Panier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController {
    #[Route('/commande', name: 'app_commande')]
    public function index(Request $request, SessionInterface $session, ProduitRepository $produitRepository, EntityManagerInterface $entityManager, Panier $panier): Response {

        $data = $panier->getPanier($session, $produitRepository);

        // Affichage de la page de commande
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($commande->isPayOnDelivery()) {

                if (!empty($data['total'])) {

                    $commande->setPrixTotal($data['total']);
                    $commande->setCreatedAt(new \DateTimeImmutable());
                    $entityManager->persist($commande);
                    $entityManager->flush();

                    foreach ($data['panier'] as $value) {
                        $commandeProduit = new CommandeProduit();
                        $commandeProduit->setCommande($commande);
                        $commandeProduit->setProduit($value['produit']);
                        $commandeProduit->setQuantite($value['quantite']);
                        $entityManager->persist($commandeProduit);
                        $entityManager->flush();
                    }
                }

                $session->set('panier', []);

                // $this->addFlash('danger', 'Le panier a bien été vidé');
    
            return $this->redirectToRoute('app_commande_message', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),
            'Total' => $data['total'],
        ]);
    }

    #[Route('/commande/message', name: 'app_commande_message')]
    public function commandeMessageAction(SessionInterface $session): Response 
    {
        // Récupérer le panier de la session
        $panier = $session->get('panier', []);
    
        // Vérifier si le panier est vide
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide. Vous ne pouvez pas passer de commande sans produits.');
            return $this->redirectToRoute('app_panier'); // Rediriger vers la page du panier
        }
    
        // Sinon, afficher le message de confirmation de commande
        $this->addFlash('success', 'Votre commande a été enregistrée. Vous recevrez un email de confirmation dans les plus brefs délais.');
    
        return $this->render('commande/commande_message.html.twig');
    }
    
    


    #[Route('/ville/{id}/livraison/prix', name: 'app_ville_livraison_prix')]
    public function villeLivraisonPrix(Ville $ville): Response {
        $villeLivraisonPrix = $ville->getFraisDePort();
        return new Response(json_encode(['status' => 200, 'message' => 'ok', 'content' => $villeLivraisonPrix]));
    }
}
