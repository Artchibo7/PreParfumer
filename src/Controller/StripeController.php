<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StripeController extends AbstractController
{
    #[Route('/payment/success', name: 'app_stripe_success')]
    public function success(): Response
    {
        return $this->render('stripe/success.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/payment/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
}
