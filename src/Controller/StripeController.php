<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart)
    // public function index(Cart $cart, OrderRepository $orderRepository, ProductRepository $productRepository, $reference)
    {
        
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        // $order = $orderRepository->findOneByReference($reference);
        // if(!order){
        //     new JsonResponse(['error' => 'order']);
        // }

        // foreach ($order->getOrderDetails()->getValues() as $product) 
        foreach ($cart->getFull() as $product) 
        {
            // $product_object = $productRepository->findOneByName($product->getProduct());

            $products_for_stripe[] = 
            [
                'price_data' => 
                [
                    'currency' => 'eur',
                    // 'unit_amount' => $product->getPrice(),
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => 
                    [
                        // 'name' => $product->getProduct(),
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()], 
                        // 'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()], 
                    ],
                ],

                'quantity' => 1,
                // 'quantity' => $product->getQuantity(),
            ];
        } 

        // $products_for_stripe[] = 
        // [
        //         'price_data' => 
        //         [
        //             'currency' => 'eur',
        //             'unit_amount' => $order->getCarriertPrice() * 100,
        //             'product_data' => 
        //             [
        //                 'name' => $order->getCarrierName(),
        //                 'images' => [$YOUR_DOMAIN], 
        //             ],
        //         ],
                
        //         'quantity' =>1,
        // ];
        

        Stripe::setApiKey('A COMPLETER');

        $checkout_session = $session::create(
        [
            // 'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            // 'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url'  => $YOUR_DOMAIN . '/cancel.html',
            // 'success_url' => $YOUR_DOMAIN . '/commande/arreur/{CHECKOUT_SESSION_ID}',

        ]);

        $response = new JsonResponse(['id' => $checkout_session->id]);

        return $response; 
    }
}
