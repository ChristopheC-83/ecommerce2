<?php

namespace App\Controller\Account;

use App\Form\NameUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NameController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-nom', name: 'app_account_modify_name')]
    public function index(
        Request $request,
    ): Response {

        $form = $this->createForm(NameUserType::class, $this->getUser());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Vos nom/prénom ont bien été modifiés');
            return $this->redirectToRoute('app_account_modify_name');
        }

        return $this->render('account/name/index.html.twig', [
            'modifyName' => $form->createView(),
        ]);

    }


}