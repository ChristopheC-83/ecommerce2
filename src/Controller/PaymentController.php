<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{



    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index($id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {


        // clé secrete récupérée dans stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);


        // il faut que la commande appartienne au User en cours
        $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser(),
        ]);

        // dd($order);

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }


        $products_for_stripe = [];

        foreach ($order->getOrderDetails() as $product) {
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($product->getProductPriceWt() * 100, 0, "", ""),   //stripe prend les nombres entiers sans virgule 1500 pour 15€00
                    'product_data' => [
                        'name' => $product->getProductName(),
                        'images' => [
                            $_ENV['DOMAIN'] . '/uploads/illustrations/' . $product->getProductIllustration(),
                        ]
                    ]
                ],
                'quantity' => $product->getProductQuantity(),

            ];
        }

        // on ajoute le transporteur au tableau des produits
        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => number_format($order->getCarrierPrice() * 100, 0, "", ""),
                'product_data' => [
                    'name' => 'Tranporteur : ' . $order->getCarrierName(),
                    //    on pourrait ajouter une image générique pour le transporteur
                ]
            ],
            'quantity' => 1,

        ];


        $checkout_session = Session::create([
            // on facilite la vie de nos users
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [
                [
                    $products_for_stripe,
                ]
            ],
            'mode' => 'payment',
            'success_url' => $_ENV['DOMAIN'] . '/success.html', //url après paiement validé

            // url de retour par l'user
            // si pb lors du pmaiement, c'est stripe qui gere !
            'cancel_url' => $_ENV['DOMAIN'] . '/mon-panier/annulation',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        // dd($order);
        $entityManager->flush();

        return $this->redirect($checkout_session->url);

    }

    #[Route('/commande/merci/{id_order}', name: 'app_payment_success')]
    public function success($id_order, OrderRepository $orderRepository): Response
    {

        dd('coucou');
    }


}
