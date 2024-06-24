<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;

class OrderCrudController extends AbstractCrudController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
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


    // fonction du changement de statut de la commande
    public function changeState($state, $order)
    {


        // modification enDB
        $order->setState($state);
        $this->em->flush();
        // informons le client du changement de statut de la commande

        $mail = new Mail();
        $vars = [
            'firstname' => $order->getUser()->getFirstname(),
            'lastname' => $order->getUser()->getLastname(),
            'id_order' => $order->getId(),
        ];

        $mail->send(
            $order->getUser()->getEmail(),
            $order->getUser()->getFirstname() . " " . $order->getUser()->getLastname(),
            'Modification du statut de votre commande',
            "order_state_".$state.".html",
            $vars
        );


    }

    public function show(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, Request $request)
    {
        $order = $context->getEntity()->getInstance();
        // dd($order);


        // on récupère l'URL de notre action "show"
        $url = $adminUrlGenerator->setController(self::class)->setAction('show')->setEntityId($order->getId())->generateUrl();

        // gestion changement statut s'il y en a un

        if ($request->get('state')) {
            $this->changeState($request->get('state'), $order);
        }

        // dd($url);

        // ici on récupère l'entité qu'on observe
        // "vas me chercher la cmde num...."
        // context travaille sur la cmde sue laquelle nous cliquons

        return $this->render('admin/order.html.twig', [
            'order' => $order,
            'current_url' => $url,
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
