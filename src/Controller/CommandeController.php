<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Ville;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Service\Panier;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController {

    public function __construct(private MailerInterface $mailer){}

    #[Route('/commande', name: 'app_commande')]
    public function index(Request $request, SessionInterface $session, ProduitRepository $produitRepository, EntityManagerInterface $entityManager, Panier $panier, CommandeRepository $commandeRepository): Response {

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

                $newCommande = $commandeRepository->find($commande->getId());

                $html = $this->renderView('mail/confirmation.html.twig', [
                    'commande' => $newCommande,
                ]);


                $email = (new Email())
                    ->from('arthur.z@hotmail.fr')
                    ->to($commande->getEmail())
                    ->subject('Confirmation de réception de votre commande')
                    ->html($html);

                    $this->mailer->send($email);

                return $this->redirectToRoute('app_commande_message', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),
            'Total' => $data['total'],
        ]);
    }

    #[Route('/admin/commande', name: 'app_commandes_show')]

    public function getAllCommande(CommandeRepository $commandeRepository, Request $request, PaginatorInterface $paginator): Response {

        $data = $commandeRepository->findBy([], ['id' => 'DESC']);

        // dd($commande);
        $commande = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            2
        );


        return $this->render('commande/commande.html.twig', [
            'commandes' => $commande
        ]);
    }

    #[Route('/admin/commande/{id}/is-completed/update', name: 'app_commandes_is_completed_update')]

    public function isCompletedUpdate($id, CommandeRepository $commandeRepository, EntityManagerInterface $entityManager): Response {
        $commande = $commandeRepository->find($id);
        $commande->setCompleted(true);

        // Sauvegarder les modifications en base de données
        $entityManager->persist($commande);
        $entityManager->flush();

        $this->addFlash('success', 'La commande a été marquée comme terminée.');
        return $this->redirectToRoute('app_commandes_show');
    }

    #[Route('/admin/commande/{id}/remove', name: 'app_commandes_remove')]
    public function removeCommande(Commande $commande, EntityManagerInterface $entityManager): Response {
        $entityManager->remove($commande);
        $entityManager->flush();

        $this->addFlash('danger', 'La commande a été supprimée.');
        return $this->redirectToRoute('app_commandes_show');
    }




    #[Route('/commande/message', name: 'app_commande_message')]
    public function commandeMessageAction(SessionInterface $session): Response {
        // Récupérer le panier de la session
        $panier = $session->get('panier', []);

        // Vérifier si le panier est vide
        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide. Vous ne pouvez pas passer de commande sans produits.');

            // Rediriger vers la page du panier
            return $this->redirectToRoute('app_panier');
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
