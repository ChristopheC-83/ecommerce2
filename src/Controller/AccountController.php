<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Form\AddressUserType;
use App\Form\EmailUserType;
use App\Form\NameUserType;
use App\Form\PasswordUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }




    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {

        return $this->render('account/index.html.twig');

    }

    #[Route('/compte/modifier-mdp', name: 'app_account_modify_pwd')]
    public function password(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {



        $user = $this->getUser();  // on récup les données du user connecté pour les envoyer au formulaire
        // dd($user);

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher,  // on envoie le passwordHasher au formulaire
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $data = $form->getData();
            // dd($data);
            // $this->addFlash('success', 'Votre mot de passe a bien été modifié');
            // return $this->redirectToRoute('app_account');

            // ici pas de persist car c'est une modifcation, pas une création de data.
            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié.'
            );
            $this->entityManager->flush();
        }


        return $this->render('account/password.html.twig', [
            'modifyPwd' => $form->createView(),

        ]);
    }

    #[Route('/compte/modifier-nom', name: 'app_account_modify_name')]
    public function changeName(
        Request $request,
    ): Response {

        $form = $this->createForm(NameUserType::class, $this->getUser());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Vos nom/prénom ont bien été modifiés');
            return $this->redirectToRoute('app_account_modify_name');
        }

        return $this->render('account/changeName.html.twig', [
            'modifyName' => $form->createView(),
        ]);

    }
    #[Route('/compte/modifier-email', name: 'app_account_modify_email')]
    public function changeEmail(
        Request $request,
    ): Response {
        $form = $this->createForm(EmailUserType::class, $this->getUser());
        // dd($form);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre adresse email a bien été modifiée');
            return $this->redirectToRoute('app_account_modify_email');
        }

        return $this->render('account/changeMail.html.twig', [
            'modifyMail' => $form->createView(),
        ]);

    }


    #[Route('/compte/adresses', name: 'app_account_addresses')]
    public function addresses(): Response
    {

        return $this->render('account/addresses.html.twig');

    }
    #[Route('/compte/adresses/delete/{id}', name: 'app_account_address_delete')]
    public function addressDelete(Request $request, $id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);
        if(!$address OR $address->getUser() != $this->getUser()){
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
    public function addressForm(Request $request, $id, AddressRepository $addressRepository): Response
    {
        if($id){
            // on récupère l'adresse à modifier en passant par entityManager
            // $address = $this->entityManager->getRepository(Address::class)->findOneById($id);
            // ou par addressRepository, mieux car séparation des responsabilités !
            $address = $addressRepository->findOneById($id);

                //  vérifions si l'adresse existe et appartient bien à l'utilisateur connecté
                if(!$address OR $address->getUser() != $this->getUser()){
                    $this->addFlash('warning', 'Cette adresse n\'existe pas ou ne vous appartient pas.');
                    return $this->redirectToRoute('app_account_addresses');
                }
        }else{
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

        return $this->render('account/addressForm.html.twig', [
            'addressForm' => $form->createView(),
        ]);

    }




}
