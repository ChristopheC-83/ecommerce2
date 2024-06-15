<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmailUserType;
use App\Form\NameUserType;
use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {

        return $this->render('account/index.html.twig');

    }

    #[Route('/compte/modifier-mdp', name: 'app_account_modify_pwd')]
    public function password(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
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
            $entityManager->flush();
        }


        return $this->render('account/password.html.twig', [
            'modifyPwd' => $form->createView(),

        ]);
    }

    #[Route('/compte/modifier-nom', name: 'app_account_modify_name')]
    public function changeName(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        $form = $this->createForm(NameUserType::class, $this->getUser());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
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
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(EmailUserType::class, $this->getUser());
        // dd($form);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre adresse email a bien été modifiée');
            return $this->redirectToRoute('app_account_modify_email');
        }


        return $this->render('account/changeMail.html.twig', [
            'modifyMail' => $form->createView(),
        ]);

    }


}
