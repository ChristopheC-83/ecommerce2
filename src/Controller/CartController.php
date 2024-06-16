<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart()
        ]);
    }



    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneById($id);
        // dd($product);
        $cart->add($product);
        $this->addFlash('success', 'Votre produit a bien été ajouté au panier');

        return $this->redirectToRoute('app_product', ['slug' => $product->getSlug()]);
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
