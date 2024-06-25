<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;

class UserCrudController extends AbstractCrudController
{
    private $security;

    // Injection du service Security dans le constructeur pour vérifier les niveaux d'accréditation
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
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
        $fields = [
            IdField::new('id')->hideOnForm(),   // ->hideOnForm() permet de cacher le champ dans le formulaire
            TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('email')->setLabel('email')->onlyOnIndex(),  // ->onlyOnIndex() permet de cacher le champ dans le formulaire
        ];

        // Ajouter le champ roles seulement si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $fields[] = ChoiceField::new('roles')
                ->allowMultipleChoices()
                ->setChoices([
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_MODO' => 'ROLE_MODO',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                ]);
        }
        // Ajouter le champ roles seulement si l'utilisateur a le rôle ROLE_ADMIN
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $fields[] = ChoiceField::new('roles')
                ->allowMultipleChoices()
                ->setChoices([
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_MODO' => 'ROLE_MODO',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                ]);
        }
        // Ajouter le champ roles seulement si l'utilisateur a le rôle ROLE_SUPER_ADMIN
        if ($this->security->isGranted('ROLE_MODO')) {
            $fields[] = ChoiceField::new('roles')
                ->allowMultipleChoices()
                ->setChoices([
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_MODO' => 'ROLE_MODO',
                ]);
        }

        return $fields;
    }

}
