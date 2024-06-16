<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/categorie/{slug}', name: 'app_category')]
    public function index($slug, CategoryRepository $categoryRepository): Response
    {
        // dd($slug);
        // $category=$categoryRepository->findAll();
        // dd($category);

        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->findOneBySlug($slug);
        if (!$category) {
            return $this->redirectToRoute('app_home');
        }
        // dd($category);

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'category' => $category
        ]);
    }
    #[Route('/categories', name: 'app_categories')]
    public function allCategoriesProducts(CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $products = $productRepository->findAll();
        // dd($category);

        return $this->render('category/allCategories.html.twig', [
            'categories' => $categories,
            'products' => $products
        ]);
    }
}
