<?php

namespace App\Controller\Stripe;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeCancelPaymentController extends AbstractController
{
    #[Route('/stripe-payment-cancel/{StripeCheckoutSessionId}', name: 'stripe-payment-cancel')]
    public function index(?Order $order): Response
    {
        if(!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
   
        
        
        return $this->render('stripe/stripe_cancel_payment/index.html.twig', [
            'controller_name' => 'StripeCancelPaymentController',
            'order' => $order,
        ]);
    }
}
