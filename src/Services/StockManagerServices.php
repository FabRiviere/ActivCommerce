<?php

namespace App\Services;

use App\Entity\Order;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class StockManagerServices
{
    private $manager;
    private $repoProduct;

    public function __construct(EntityManagerInterface $manager, ProductRepository $repoProduct)
    {
        $this->manager = $manager;
        $this->repoProduct = $repoProduct;
    }

    public function deStock(Order $order)
    {
        // On récupére les détails de la commande ou sont stockés ts ls produits
        $orderDetails = $order->getOrderDetails()->getValues();

        // on créer une boucle pour chaque produit , on récupére son nom
        foreach($orderDetails as $key => $details) {
            $product = $this->repoProduct->findByName($details->getProductName())[0];

            // On calcule la quantité en enlevant la quantité de produit commandés
            $newQuantity = $product->getQuantity() - $details->getQuantity();
            // On met à jour la quantité selon la décrémentation précédente
            $product->setQuantity($newQuantity);
            // On met à jour en base de données
            $this->manager->flush();
        }
    }
}