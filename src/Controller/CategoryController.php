<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
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
        // dd($category);

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'category' => $category
        ]);
    }
    #[Route('/categories', name: 'app_categories')]
    public function allCategoriesProducts(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        // dd($category);

        return $this->render('category/allCategories.html.twig', [
            'categories' => $categories
        ]);
    }
}
