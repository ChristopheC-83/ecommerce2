<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    #[Route('/commande/paiement', name: 'app_payment')]
    public function index(): Response
    {
        // clé secrete récupérée dans stripe
        Stripe::setApiKey('sk_test_51PIk33P2eXw7cLZ1QugSS4eVxQ7dIkiGxpCxzeuMKkla43l0OXUkmlxOExRzOrBx0ztZqD3eHAGkdEJnvAu46Fdd00fxoWbFAv');
        $YOUR_DOMAIN = 'https://127.0.0.1:8000';

        $checkout_session = Session::create([
            'line_items' => [
                [
                    # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => 6969,   //stripe prend les nombres entiers sans virgule 1500 pour 15€00
                        'product_data' => [
                            'name' => 'pdt de test'
                        ]
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html', //url après paiement validé
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',   //url de retour ou d'echec de paiement
        ]);

       

        return $this->redirect($checkout_session->url);

    }
}
