<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            // ...
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        // invariable
        // email
        // password

        // variable
        // nom et prénom




        return [
            //  ici pas des tableau comme dans le formulaires mais des méthodes, ce sont des champs, pas des imput !
            IdField::new('id')->hideOnForm(),   // ->hideOnForm() permet de cacher le champ dans le formulaire
            TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('email')->setLabel('email')->onlyOnIndex(),  // ->onlyOnIndex() permet de cacher le champ dans le formulaire
        ];
    }

}
