<?php

namespace App\Controller;

use App\Entity\HistoriqueProduit;
use App\Entity\Produit;
use App\Form\HistoriqueProduitType;
use App\Form\ProduitType;
use App\Repository\HistoriqueProduitRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger ): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            if ($image) {
                $originalFileNom = pathinfo($image->getClientOriginalName(), flags:PATHINFO_FILENAME);
                // Slugger permet de supprimer les éspaces dans les noms des images
                $safeFileNom = $slugger->slug($originalFileNom);
                $nouveauFileNom = $safeFileNom.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_dir'),
                        $nouveauFileNom
                    );
                }
                catch (FileException $e) {}

                $produit->setImage($nouveauFileNom);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $historiqueStock = new HistoriqueProduit();
            $historiqueStock->setQuantite($produit->getStock());
            $historiqueStock->setProduit($produit);
            $historiqueStock->setDateAjout(new \DateTimeImmutable());

            $entityManager->persist($historiqueStock);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté');

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été modifié');

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();

            $this->addFlash('danger', 'Le produit a bien été supprimé');
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/ajout/produit/{id}/stock', name: 'app_produit_stock_ajout', methods: ['POST', 'GET'])]
    public function ajoutStock($id, EntityManagerInterface $entityManager, Request $request, ProduitRepository $produitRepository): Response
    {
        $ajoutStock = new HistoriqueProduit();
        $form = $this->createForm(HistoriqueProduitType::class, $ajoutStock);
        $form->handleRequest($request);

        $produit = $produitRepository->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($ajoutStock->getQuantite() > 0) {
            $newQantite = $produit->getStock() + $ajoutStock->getQuantite();
            $produit->setStock($newQantite);
            $ajoutStock->setProduit($produit);
            $ajoutStock->setDateAjout(new \DateTimeImmutable());
            $entityManager->persist($ajoutStock);
            $entityManager->flush();

            $this->addFlash('success', 'Le stock a bien été ajouté');

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }else{
            $this->addFlash('danger', 'La quantité doit être superieure a 0');

            return $this->redirectToRoute('app_produit_stock_ajout', ['id'=>$produit->getId], Response::HTTP_SEE_OTHER);
        }
    }
        return $this->render('produit/ajoutStock.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit  
        ]);
    }

    #[Route('/ajout/produit/{id}/stock/historique', name: 'app_produit_stock_ajout_historique', methods: ['GET'])]

    public function ajoutStockHistorique($id, ProduitRepository $produitRepository, HistoriqueProduitRepository $historiqueProduitRepository): Response
    {
        $produit = $produitRepository->find($id);
        $ajoutStockHistorique = $historiqueProduitRepository->findBy(['produit' => $produit], ['id' => 'DESC']);

        return $this->render('produit/showHistoriquestock.html.twig', [
            'produitsAjouter' => $ajoutStockHistorique,
            
        ]);
    }
}  