<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Image')
            ->setEntityLabelInPlural('Images')
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {

        $required = true;

        if ($pageName == 'edit') {
            $required = false;
        }

        return [
            TextField::new('title', "Titre"),
            TextareaField::new('content', "Contenu"),
            TextField::new('buttonTittle', "Titre du Bouton"),
            TextField::new('buttonLink', "URL du Bouton"),
            ImageField::new('illustration')
                ->setLabel('image de fond')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')->setUploadDir('/public/uploads/illustrations')
                ->setBasePath('uploads/illustrations')
                ->setRequired($required)
                ->setHelp('image de fond du Header en 800x520px'),
        ];
    }
    
}
