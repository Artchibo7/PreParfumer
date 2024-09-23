<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'produits' => $produitRepository->findBy([], ['id' => 'DESC']),
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/home/produit/{id}/show', name: 'app_home_produit_show', methods: ['GET'])]
    public function show(Produit $produit, ProduitRepository $produitRepository): Response
    {

        $dernierProduit = $produitRepository->findBy([], ['id' => 'DESC'], limit:6);
        return $this->render('home/show.html.twig', [
            'produit' => $produit,
            'produits' => $dernierProduit
        ]);
    }
}
