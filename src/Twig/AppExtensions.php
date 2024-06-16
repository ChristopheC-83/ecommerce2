<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
        ];
    }
    public function getGlobals(): array
    {
        return [
            'Nom_Variable' => 'Valeur de la variable',
            'allCategories' => $this->categoryRepository->findAll(),
        ];
    }
    public function formatPrice($number)
    {
        return number_format($number, 2, ',', ' ') . ' €';
    }
}