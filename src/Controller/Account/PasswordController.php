<?php

namespace App\Controller\Account;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-mdp', name: 'app_account_modify_pwd')]
    public function index(
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


        return $this->render('account/password/index.html.twig', [
            'modifyPwd' => $form->createView(),

        ]);
    }


}