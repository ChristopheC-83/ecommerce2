<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'getTotal' => $cart->getTotal(),
            'getTotalWt' => $cart->getTotalWt(),
        ]);
    }


    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request): Response
    {
        // renvoie la derniere url visitée
        // dd($request->headers->get('referer'));
        $product = $productRepository->findOneById($id);
        // dd($product);
        $cart->add($product);
        $this->addFlash('success', 'Votre produit a bien été ajouté au panier');

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart): Response
    {
        
        $cart->decrease($id);

        $this->addFlash('success', 'Votre produit a bien été retiré du panier');

        return $this->redirectToRoute('app_cart');
    }


    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart): Response
    {
        // dd($product);
        $cart->remove();
        $this->addFlash('warning', 'Votre panier a bien été vidé.');

        return $this->redirectToRoute('app_home');
    }
}
