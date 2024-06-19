<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('commande')
            ->setEntityLabelInPlural('commandes')
            // ...
        ;
    }

    public function configureActions(Actions $actions): Actions
    {

        $show = Action::new('show', 'Voir la commande', 'fa fa-eye')
            ->linkToCrudAction('show');// ici show est une fonction que l'on va créer



        return $actions
            // on retire 3 actions de base de la page index
            ->remove(Crud::PAGE_INDEX, Action::NEW )
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            // on ajoute l'action détail pour regarder une commande en détail, mais pas génial....
            // ->add(Crud::PAGE_INDEX, Action::DETAIL)
            //  on va le faire nous même...
            ->add(Crud::PAGE_INDEX, $show)
        ;
    }

    public function show(AdminContext $context)
    {

        // ici on récupère l'entité qu'on observe
        // "vas me chercher la cmde num...."
        // context travaille sur la cmde sue laquelle nous cliquons

        $order = $context->getEntity()->getInstance();
        dd($order);

        return $this->render('admin/order.html.twig', [
            'order' => $order,
        ]);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt', 'Date de création'), // remplace le setLabel
            NumberField::new('state')->setLabel('Statut de la commande')->setTemplatePath('admin/state.html.twig'),
            AssociationField::new('user')->setLabel('client'),
            TextField::new('carrierName')->setLabel('Transporteur'),
            NumberField::new('totalTVA')->setLabel('TOTAL TVA'),
            NumberField::new('totalWt')->setLabel('TOTAL TTC'),

        ];
    }

}
