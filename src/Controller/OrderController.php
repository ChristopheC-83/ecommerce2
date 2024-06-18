<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    // 1ere phase du tunnel d'achat
    //  choix de l'adresse de livraison et du transporteur


    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        $addresses = $this->getUser()->getAddresses();

        if (count($addresses) == 0) {
            $this->addFlash('warning', 'Vous devez ajouter une adresse pour passer commande');
            return $this->redirectToRoute('app_account_address_form');
        }
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            // url de la page suivante si différente de la page actuelle après soumission
            'action' => $this->generateUrl('app_order_summary'),
        ]);



        return $this->render('order/index.html.twig', [
            'deliveryForm' => $form->createView(),
        ]);
    }
    // 2eme phase du tunnel d'achat
    // recap cmde et insertion DB
    // preparation paiement vers stripe

    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManagerInterface): Response
    {
        // si info passées par une autre méthod, l'user qui appuie sur Enter par exemple, on repart au panier
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }

        // on récupère les produits du panier
        $products = $cart->getCart();

        // on récupère les données du formulaire de la page précédente
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),

        ]);

        // on écoute le formulaire de la page précédente
        $form->handleRequest($request);

        // On teste le formulaire
        // on envoie les coordonnées de livraison dans order
        // on crée une ligne par produit dans orderDetail. Chaque ligne est raccrochée à sa commande
        // on envoie le tout en DB
        if ($form->isSubmitted() && $form->isValid()) {

            // creation de la chaine adresse
            $addressObject = $form->get('addresses')->getData();
            $address = $addressObject->getFirstname() . " " . $addressObject->getLastname() . "<br>";
            $address .= $addressObject->getAddress() . "<br>";
            $address .= $addressObject->getPostal() . " " . $addressObject->getCity() . "<br>";
            $address .= $addressObject->getCountry() . "<br>";
            $address .= $addressObject->getPhone();

            // dd($address);
            // dd($cart);


            //  envoi panier en DB => le tranporteur, la date de passage de cmde puis création des articles raccrochées à la cmde
            $order = new Order();
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);

            // pour chaque produit du panier, nous récupérons ses données et le raccrochons à la commande
            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductTVA($product['object']->getTVA());
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);
            }

            // On persiste puis on flush en DB
            $entityManagerInterface->persist($order);
            $entityManagerInterface->flush();

        }




        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'totalWt' => $cart->getTotalWt(),

        ]);
    }


}
