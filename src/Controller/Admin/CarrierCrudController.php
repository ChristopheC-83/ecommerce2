<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class CarrierCrudController extends AbstractCrudController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
          // Vérification des rôles pour accéder à ce contrôleur
          if (!$this->security->isGranted('ROLE_SUPER_ADMIN') && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }
    }
    public static function getEntityFqcn(): string
    {
        return Carrier::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('transporteur')
            ->setEntityLabelInPlural('transporteurs')
            // ...
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom')->setHelp('Nom du transporteur'),
            TextareaField::new('description')->setLabel('informations complémentaires sur le transporteur'),
            NumberField::new('price')->setLabel('prix HT')->setHelp('prix TTC du transport sans le sigle de la monnaie €'),
        ];
    }
}
