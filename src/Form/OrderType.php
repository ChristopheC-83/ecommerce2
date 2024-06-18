<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Carrier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addresses', EntityType::class, [
                'required' => true,
                'class' => Address::class, // entité en lien avec le champ
                'label' => '<b>Choisissez votre adresse de livraison</b>',
                'expanded' => true,  // affiche les adresses sous forme de radio plutot qu'en Select
                'choices' => $options['addresses'], // les adresses à afficher
                'label_html' => true, // pour afficher le __toString() de l'entité
                'attr' => [
                    'class' => 'bg-light p-3 rounded-lg ',
                ],
                
            ])
            ->add('carriers', EntityType::class, [
                'label' => '<b>Choisissez votre mode de livraison</b>',
                'required' => true,
                'class' => Carrier::class, // entité en lien avec le champ
                'expanded' => true,  // affiche les adresses sous forme de radio plutot qu'en Select
                'label_html' => true, // pour afficher le __toString() de l'entité
                'attr' => [
                    'class' => 'bg-light p-3 rounded-lg ',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Récapitulatif de ma commande",
                'attr' => [
                    'class' => 'btn btn-dark w-100 mt-2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'addresses' => null,
        ]);
    }
}
