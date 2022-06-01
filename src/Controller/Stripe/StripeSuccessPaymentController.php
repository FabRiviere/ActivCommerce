<?php

namespace App\Controller\Stripe;

use App\Entity\Order;
use App\Services\CartServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeSuccessPaymentController extends AbstractController
{
    #[Route('/stripe-payment-success/{StripeCheckoutSessionId}', name: 'stripe-payment-success')]
    public function index(?Order $order, CartServices $cartServices,
                           EntityManagerInterface $manager ): Response
    {
        if(!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if($order->isIsPaid()) {
            // commande payée
            $order->setIsPaid(true);
            $manager->flush();
            // suppression du panier
            $cartServices->deleteCart();
            // un mail au client

            //
        }

        return $this->render('stripe/stripe_success_payment/index.html.twig', [
            'controller_name' => 'StripeSuccessPaymentController',
            'order' => $order,
        ]);
    }
}
