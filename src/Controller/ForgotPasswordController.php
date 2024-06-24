<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ForgotPasswordFormType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {

        $this->em = $entityManagerInterface;

    }

    #[Route('/mdp-oublie', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository): Response
    {

        // 1 formulaire
        $form = $this->createForm(ForgotPasswordFormType::class);

        $form->handleRequest($request);

        // 2 traitement

        if ($form->isSubmitted() && $form->isValid()) {

            // 3 email exite en DB
            // dd($form->getData());
            $email = $form->get('email')->getData();
            // dd($email);
            $user = $userRepository->findOneByEmail($email);
            // dd($user);

            // 5 si pas d'email trouvé, on le notifie, vaguement...
            $this->addFlash('info', 'Si vous êtes inscrit, un email de réinitialisation vous a été envoyé par email.');

            if ($user) {
                //  4 on reset le password qu'on envoie par email

                // 6-1 creation d'un token qu'on va stocker en DB dans le USer avec une date d'expiration
                $randomBytes = random_bytes(15);
                $token = bin2hex($randomBytes);
                $user->setToken($token);

                //  6-2 on limite le token dans le temps
                $date = new DateTime();
                $date->modify('+10 minutes');
                // dd($date);
                $user->setTokenExpireAt($date);

                //  on met token et date en DB
                $this->em->flush();
                // dd($user);

                //  6-3 on génére un lien sécurisé
                $link = $this->generateUrl('app_password_reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);


                // FINAL envoi de l'email avec le lien sécurisé
                $mail = new Mail();
                $vars = [
                    'link' => $link,
                ];

                $mail->send(
                    $user->getEmail(),
                    $user->getFirstname() . " " . $user->getLastname(),
                    'Mot de passe oublié ?',
                    "forgotPassword.html",
                    $vars
                );
            }
        }

        return $this->render('forgotPassword/index.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }


    #[Route('/mdp-oublie/reset/{token}', name: 'app_password_reset')]
    public function reset(Request $request, UserRepository $userRepository): Response
    {
        // si le token n'existe pas
        if (!$token = $request->get('token')) {
            return $this->redirectToRoute('app_password');
        }

        // si le token existe, on cherche l'utilisateur
        $user = $userRepository->findOneByToken($token);

        // si l'utilisateur n'existe pas
        if (!$user) {
            return $this->redirectToRoute('app_password');
        }

        // on compare la date du token à l'instant T
        $now = new DateTime();
        if ($now > $user->getTokenExpireAt()) {
            return $this->redirectToRoute('app_password');
        }




        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());
            $user->setToken(null);
            $user->setTokenExpireAt(null);
            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié.'
            );
            $this->em->flush();
            return $this->redirectToRoute('app_login');
        }


        return $this->render('forgotPassword/reset.html.twig', [
            'form' => $form->createView(),

        ]);
    }
}
