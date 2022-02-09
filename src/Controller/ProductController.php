<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="product")
     */
    public function show($slug, ProductRepository $productRepository): Response
    {
       
        $product = $productRepository->findOneBySlug($slug);

        if(!$product)
        {
            return $this->redirectToRoute('products');
        }
    
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
