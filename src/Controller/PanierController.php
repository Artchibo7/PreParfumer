<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class PanierController extends AbstractController {

    public function __construct(private readonly ProduitRepository $produitRepository) {
    }
    #[Route('/panier', name: 'app_panier', methods: ['GET'])]
    // Pour stocker en session sans interoger la bdd on utilise SessionInterface
    public function index(SessionInterface $session): Response {
        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $idProduit => $quantite) {
            $panierWithData[] = [
                'produit' => $this->produitRepository->find($idProduit),
                'quantite' => $quantite,
            ];
        }
        // une fois que array_map ait finit de traiter tous les produits, on peut calculer le total avec array_sum
        $total = array_sum(array_map(function ($item) {
            return $item['produit']->getPrix() * $item['quantite'];
        }, $panierWithData));

        // dd($panierWithData);
        // dd($total);

        return $this->render('panier/index.html.twig', [
            'items' => $panierWithData,
            'total' => $total
        ]);
    }

    #[Route('/panier/ajout/{id}', name: 'app_panier_new', methods: ['GET'])]

    public function ajoutAuPanier($id, SessionInterface $session): Response {

        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);

        $this->addFlash('success', 'Le produit a bien été ajouté au panier');

        return $this->redirectToRoute('app_panier', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/panier/supprimer/{id}', name: 'app_panier_supprimer', methods: ['GET'])]
    public function supprimerPanier($id, SessionInterface $session): Response {

        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        $this->addFlash('danger', 'Le produit a bien été supprimé du panier');

        return $this->redirectToRoute('app_panier', [], Response::HTTP_SEE_OTHER);
    }

    // #[Route('/panier/vider', name: 'app_panier_vider', methods: ['GET'])]
    // public function viderPanier(SessionInterface $session): Response {

    //     $session->set('panier', []);

    //     $this->addFlash('danger', 'Le panier a bien été vidé');

    //     return $this->redirectToRoute('app_panier', [], Response::HTTP_SEE_OTHER);
    // }

    #[Route('/panier/vider', name: 'app_panier_vider', methods: ['GET'])]
    public function viderPanier(SessionInterface $session): Response {

        $panier = $session->get('panier', []);

        if (empty($panier)) {

            $this->addFlash('warning', 'Le panier est déjà vide');

        } else {

            $session->set('panier', []);

            $this->addFlash('danger', 'Le panier a bien été vidé');

        }

        return $this->redirectToRoute('app_panier', [], Response::HTTP_SEE_OTHER);
    }
}
