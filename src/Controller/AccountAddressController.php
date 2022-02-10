<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    /**
     * @Route("/compte/adresses", name="account_address")
     */
    public function index(): Response
    {

        return $this->render('account/address.html.twig');
    }

    /**
     * @Route("/compte/ajouter-une-adresse", name="account_address_add")
     */
    public function add(Cart $cart,Request $request, EntityManagerInterface $em): Response
    {
        $address = new Address;

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $address->setUser($this->getUser());
            $em->persist($address);
            $em->flush();

            //si j'ai des produits dans mon panier
            
            if($cart->get())
            {
                return $this->redirectToRoute('order');
            }
            else{
                return $this->redirectToRoute('account_address');
            }
           
        }
        
        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/modifier-une-adresse/{id}", name="account_address_edit")
     */
    public function edit(Request $request, $id, AddressRepository $addressRepository ,EntityManagerInterface $em): Response
    {

        $address = $addressRepository->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
          
            $em->flush();    
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView() 
        ]);
    }

    /**
     * @Route("/compte/supprimer-une-adresse/{id}", name="account_address_delete")
     */
    public function delete($id, AddressRepository $addressRepository ,EntityManagerInterface $em): Response
    {

        $address = $addressRepository->findOneById($id);

        if($address && $address->getUser() == $this->getUser())
        {
            $em->remove($address);
            $em->flush();
        }     

        return $this->redirectToRoute('account_address');
  
    }
}
