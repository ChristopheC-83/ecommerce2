<?php

namespace App\Controller\Account;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WishlistController extends AbstractController
{
    #[Route('/compte/liste-de-souhaits', name: 'app_account_wishlist')]
    public function index(): Response
    {





        return $this->render('account/wishlist/index.html.twig', [



        ]);
    }

    #[Route('/compte/liste-de-souhaits/add/{id}', name: 'app_account_wishlist_add')]
    public function add($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        //  1 on recup le produit
        $product = $productRepository->findOneById($id);

        //  2 ajout produit à wishlist (le user est forcément connecté ici)
        if ($product) {
            $this->getUser()->addWishlist($product);

            // 3 on enregistre en bdd
            $entityManager->flush();

            $this->addFlash('success', 'Votre produit a bien été ajouté à votre liste de souhaits.');
        }

        return $this->redirect($request->headers->get('referer'));
    }


    #[Route('/compte/liste-de-souhaits/remove/{id}', name: 'app_account_wishlist_remove')]
    public function remove($id, ProductRepository $productRepository,Request $request,EntityManagerInterface $entityManager): Response
    {
        //  1 on recup le produit
        $product = $productRepository->findOneById($id);

        //  2 ajout produit à wishlist (le user est forcément connecté ici)
        if ($product) {
            $this->getUser()->removeWishlist($product);

            // 3 on enregistre en bdd
            $entityManager->flush();
            $this->addFlash('info', 'Votre produit a bien été retiré de votre liste de souhaits.');
        } else{
            $this->addFlash('danger', 'Ce produit n\'existe pas dans votre liste de souhaits.');
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
