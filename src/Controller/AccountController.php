<?php

namespace App\Controller;

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
         ): Response
    {

      

        $user = $this->getUser();  // on récup les données du user connecté pour les envoyer au formulaire
        // dd($user);

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher,  // on envoie le passwordHasher au formulaire
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
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
}
