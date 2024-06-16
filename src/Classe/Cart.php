<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{


    public function __construct(private RequestStack $requestStack)
    {



    }

    public function add($product)
    {
        // dd($product);
        // on doit
        // appeler session symfo, elle fera transiter l'objet panier/cart de page en page
        $session = $this->requestStack->getSession();
        // récupérer le panier actuel
        $cart = $this->requestStack->getSession()->get('cart');
        // dd($session);

        //  si le produit est déjà présent, on rajoute une unité
        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => $cart[$product->getId()]['qty'] + 1
            ];
        } else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }
        // créer session Cart
        $this->requestStack->getSession()->set('cart', $cart);

        // dd($this->requestStack->getSession()->get('cart'));

    }

    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }

    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }



}

