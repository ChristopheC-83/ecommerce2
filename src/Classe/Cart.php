<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{


    public function __construct(private RequestStack $requestStack)
    {



    }

    // ajouter un produit dans le panier
    public function add($product)
    {
        // dd($product);
        // on doit
        // appeler session symfo, elle fera transiter l'objet panier/cart de page en page
        $session = $this->requestStack->getSession();
        // récupérer le panier actuel
        $cart = $this->getCart();
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

        // dd($this->getCart());

    }


    // retirer un produit du panier
    public function decrease($id)
    {
        $cart = $this->getCart();

        if ($cart[$id]['qty'] > 1) {
            $cart[$id]['qty'] = $cart[$id]['qty'] - 1;
        } else {
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart);

    }

    // retourne la quantité totale d'articles dans le panier
    public function fullQuantity()
    {
        $cart = $this->getCart();
        $quantity = 0;

        if (!isset($cart)) {
            return $quantity;
        }

        foreach ($cart as $item) {
            $quantity += $item['qty'];
        }
        // dd($quantity);
        return $quantity;
    }

    // retourne le montant total HT du panier
    public function getTotal()
    {
        $cart = $this->getCart();
        $price = 0;

        if (!isset($cart)) {
            return $price;
        }

        foreach ($cart as $product) {
            $price += $product['object']->getPrice() * $product['qty'];
        }
        return $price;
    }

    // retourne le montant total TTC du panier
    public function getTotalWt()
    {
        $cart = $this->getCart();
        $price = 0;

        if (!isset($cart)) {
            return $price;
        }

        foreach ($cart as $product) {
            $price += $product['object']->getPriceWt() * $product['qty'];
        }
        return $price;
    }

    // retourne le contenu du panier
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }

    // vider le panier totalement
    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }



}

