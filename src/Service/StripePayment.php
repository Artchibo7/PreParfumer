<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePayment {
    private $redirectUrl;

    public function __construct() {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);
        Stripe::setApiVersion('2024-09-30.acacia');
    }

    public function startPayment($panier, $fraisDePort) {
        // Récupère les produits du panier
        $data = $panier;; 
        $panierProduits = $data['panier'];
        $produits = [
           [
            'quantite' => 1,
            'price' => $fraisDePort,
            'name' => 'Frais de livraison'
           ]
        ]; // On initialise correctement le tableau

        foreach ($panierProduits as $value) {
            $produitItem = [];
            $produitItem['name'] = $value['produit']->getNom(); // Correction de l'appel de la méthode
            $produitItem['price'] = $value['produit']->getPrix(); // Correction de l'appel de la méthode
            $produitItem['quantite'] = $value['quantite']; // Quantité
            $produits[] = $produitItem; // On ajoute au tableau des produits
        }

        // Création de la session Stripe en dehors de la boucle
        $session = Session::create([
            'line_items' => array_map(function ($produit) {
                return [
                    'quantity' => $produit['quantite'],
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $produit['name'], // Utilisation correcte de name
                        ],
                        'unit_amount' => $produit['price'] * 100, // Stripe prend le montant en centimes
                    ],
                ];
            }, $produits),
            'mode' => 'payment',
            'cancel_url' => 'https://localhost:8000/payment/cancel', // je peux remplacer par $this->redirectUrl si nécessaire
            'success_url' => 'https://localhost:8000/payment/success',
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR']
            ],
            'metadata' => [
                
            ],
        ]);

        // On stocke l'URL de redirection
        $this->redirectUrl = $session->url;
    }

    public function getStripeRedirectUrl() {
        return $this->redirectUrl;
    }
}
