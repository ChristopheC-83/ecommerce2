<?php

namespace App\Controller\Account;

use App\Entity\Address;
use App\Form\AddressUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddressesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/compte/adresses', name: 'app_account_addresses')]
    public function index(): Response
    {

        return $this->render('account/address/index.html.twig');

    }
    #[Route('/compte/adresses/delete/{id}', name: 'app_account_address_delete')]
    public function delete(Request $request, $id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);
        if (!$address or $address->getUser() != $this->getUser()) {
            $this->addFlash('warning', 'Cette adresse n\'existe pas ou ne vous appartient pas.');
            return $this->redirectToRoute('app_account_addresses');
        }

        $this->entityManager->remove($address);
        $this->entityManager->flush();

        $this->addFlash('info', 'Cette adresse a bien été supprimée.');
        return $this->redirectToRoute('app_account_addresses');
    }

    // On va utiliser le même formulaire pour ajouter et modifier une adresse
    //  le slug id est optionnel, si on a un id, c'est qu'on modifie une adresse, sinon on en ajoute une

    #[Route('/compte/adresse/ajouter/{id}', name: 'app_account_address_form', defaults: ['id' => null])]
    public function form(Request $request, $id, AddressRepository $addressRepository): Response
    {
        if ($id) {
            // on récupère l'adresse à modifier en passant par entityManager
            // $address = $this->entityManager->getRepository(Address::class)->findOneById($id);
            // ou par addressRepository, mieux car séparation des responsabilités !
            $address = $addressRepository->findOneById($id);

            //  vérifions si l'adresse existe et appartient bien à l'utilisateur connecté
            if (!$address or $address->getUser() != $this->getUser()) {
                $this->addFlash('warning', 'Cette adresse n\'existe pas ou ne vous appartient pas.');
                return $this->redirectToRoute('app_account_addresses');
            }
        } else {
            //  on crée une nouvelle adresse
            $address = new Address();
            // on set le user connecté pour l'user_id
            $address->setUser($this->getUser());
        }


        $form = $this->createForm(AddressUserType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($address);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre adresse a bien été enregistrée.');
            return $this->redirectToRoute('app_account_addresses');
        }

        return $this->render('account/address/form.html.twig', [
            'addressForm' => $form->createView(),
        ]);

    }



}