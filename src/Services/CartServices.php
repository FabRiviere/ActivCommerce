<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;


class CartServices {


    private $requestStack;
    private $repoProduct;

    public function __construct(RequestStack $session, ProductRepository $repoProduct) 
    {
        $this->requestStack = $session;
        $this->repoProduct = $repoProduct;
    }

    public function addToCart($id)
    {
        
        $cart = $this->getCart();
        if(isset($cart[$id])) {
            // produit déjà dans le panier
            $cart[$id]++;
        } else {
            // le produit n'est pas encore dns le panier
            $cart[$id] = 1;
        }
        $this->updateCart($cart);
    }
    
    public function deleteFromCart($id)
    {
        $cart = $this->getCart();

        // produit déjà dans le panier
        if(isset($cart[$id])) {
            // produit existe plus d'une fois
            if($cart[$id] > 1) {
                // on décrémente du panier
                $cart[$id]--;
            } else {
                // Si existe une seule fois, on enlève le produit du panier
                unset($cart[$id]);
            }
            // Mettre à jour le panier
            $this->updateCart($cart);
        }

    }

    public function deleteAllToCart($id) 
    {
        $cart = $this->getCart();

        // produit déjà dans le panier
        if(isset($cart[$id])) {
            // on enlève le produit du panier
            unset($cart[$id]);
           
            // Mettre à jour le panier
            $this->updateCart($cart);
        }
    }

    public function deleteCart()
    {
        // Si on vide le panier on met à jour et affiche un tableau vide
        $this->updateCart([]);
    }

    public function updateCart($cart)
    {
        $session = $this->requestStack->getSession();

        $session->set('cart', $cart);
    }

    public function getCart() 
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    public function getFullCart()
    {
        $cart = $this->getCart();

        $fullCart = [];

        foreach ($cart as $id => $quantity) 
        {
            $product = $this->repoProduct->find($id);
            // Produit récupéré avec succès
            if($product) {
                $fullCart[] = 
                    [
                        "quantity" => $quantity,
                        "product" => $product,
                    ];
            } else {
                // id incorrecte
                $this->deleteFromCart($id);
            }
        }
        return $fullCart;
    }

}