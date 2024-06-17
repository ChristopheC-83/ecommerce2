<?php

namespace App\Controller\Account;

use App\Form\EmailUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmailController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-email', name: 'app_account_modify_email')]
    public function index(
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

        return $this->render('account/email/index.html.twig', [
            'modifyMail' => $form->createView(),
        ]);

    }


}