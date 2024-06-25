<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un véhicule')
            ->setEntityLabelInPlural('Véhicules')
            // ...
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        //  ce tricks $required permet de 
        // rendre le champs obligatoire lors de la création ET 
        // rendre facultatif lors de la modification
        $required = true;

        if ($pageName == 'edit') {
            $required = false;
        }

        return [
            TextField::new('name')->setLabel('désignation')->setHelp('nom du véhicule'),
            BooleanField::new('isHomePage')->setLabel('Mettre en avant ?')->setHelp('Mettre en 1 Sur la HomePage'),
            SlugField::new('slug')->setLabel('URL')->setTargetFieldName('name')->hideOnIndex()->setHelp('URL du véhicule généré automatiquement !'),
            TextEditorField::new('description')->setLabel('description')->setHelp('description du véhicule'),
            ImageField::new('illustration')
                ->setLabel('image')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')->setUploadDir('/public/uploads/illustrations')
                ->setBasePath('uploads/illustrations')
                ->setRequired($required)
                ->setHelp('image du véhicule en 800x520px'),
            NumberField::new('price')->setLabel('prix HT')->setHelp('prix du véhicule HT sans le sigle de la monnaie €'),
            ChoiceField::new('tva')->setLabel('TVA')->setHelp('TVA du véhicule')->setChoices([
                '5.5%' => '5.5',
                '10%' => '10',
                '20%' => '20',
            ]),
            AssociationField::new('category', 'Catégorie associée')->setHelp('catégorie du véhicule'),
        ];
    }
}
