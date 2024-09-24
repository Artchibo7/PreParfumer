<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Repository\SousCategorieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository, CategorieRepository $categorieRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $data = $produitRepository->findBy([], ['id' => 'DESC']);
        $produits = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('home/index.html.twig', [
            'produits' => $produits,
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/home/produit/{id}/show', name: 'app_home_produit_show', methods: ['GET'])]
    public function show(Produit $produit, ProduitRepository $produitRepository, CategorieRepository $CategorieRepository, Request $request, PaginatorInterface $paginator): Response
    {
        
        $data = $produitRepository->findBy([], ['id' => 'DESC']);
        $produits = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            4
        ); 

        $dernierProduit = $produitRepository->findBy([], ['id' => 'DESC']);
        return $this->render('home/show.html.twig', [
            'produit' => $produit,
            'produits' => $produits,
            'dernierProduit' => $dernierProduit,
            'categories' => $CategorieRepository->findAll()
        ]);
    }

    #[Route('/home/produit/sousCategorie{id}/filter', name: 'app_home_produit_filter', methods: ['GET'])]
    public function filter($id, ProduitRepository $produitRepository, SousCategorieRepository $sousCategorieRepository, CategorieRepository $CategorieRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // Récupérer la sous-catégorie et ses produits
        $SousCategorie = $sousCategorieRepository->find($id);
        $produitsQuery = $SousCategorie->getProduits(); // Cela retourne une PersistentCollection
    
        // Paginer les produits de la sous-catégorie
        $pagination = $paginator->paginate(
            $produitsQuery, // Collection à paginer
            $request->query->getInt('page', 1), // Numéro de page
            4 // Limite par page
        );
    
        // Rendre la vue avec les données paginées
        return $this->render('home/filter.html.twig', [
            'produits' => $pagination, // Produits paginés
            'SousCategorie' => $SousCategorie,
            'categories' => $CategorieRepository->findAll(),
        ]);
    }

    #[Route('/catalogue', name: 'app_catalogue_index', methods: ['GET'])]
    public function catalogue(ProduitRepository $produitRepository, CategorieRepository $CategorieRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $data = $produitRepository->findBy([], ['id' => 'DESC']);
        $produits = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('home/catalogue.html.twig', [
            'produits' => $produits,
            'categories' => $CategorieRepository->findAll()
        ]);
    }

    
}
