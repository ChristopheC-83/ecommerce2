<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]

    //  on appelle une injection de dépendance, index fonctionnera avec Request utilisable dans une variable, ici, $request
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        // dd($request);

        $form = $this->createForm(RegisterUserType::class, $user);  // les infos $form->getData() iront dans l'objet $user

        // Si formulaire soumis en écoutant la request
        $form->handleRequest($request);
        // si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());

            // enregistres les datas et DB, injection de dépendance avec EntityManagerInterface
            $entityManager->persist($user);   // fige les données
            $entityManager->flush();          // envoi les données
            $this->addFlash('success', 'Votre compte a bien été créé ! Vous pouvez vous connecter.');  // message flash de confirmation

            return $this->redirectToRoute('app_login');  // redirection vers la page de connexion

        }
        // potentiellement, envoi un message de confirmation

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}