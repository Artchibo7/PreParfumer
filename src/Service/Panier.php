<?php

namespace App\Service;

use App\Repository\ProduitRepository;

class Panier
{
    public function __construct(private readonly ProduitRepository $produitRepository) {
    }
    public function getPanier($session, $produitRepository): array {

        $panier = $session->get('panier', []);
        $panierWithData = [];
    
        foreach ($panier as $idProduit => $quantite) {
            $produit = $produitRepository->find($idProduit);
    
            if ($produit) {
                $panierWithData[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                ];
            }
        }
    
        $total = array_sum(array_map(function ($item) {
            return $item['produit']->getPrix() * $item['quantite'];
        }, $panierWithData));

        return [
            'panier' => $panierWithData,
            'total' => $total,
            // 'quantite' => $quantite
        ];
    
    }
}