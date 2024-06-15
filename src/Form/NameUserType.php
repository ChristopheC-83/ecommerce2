<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class NameUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', TextType::class, [
            'label' => 'Votre prénom',
            'constraints' => [
                new Length([
                    'min' => 2,
                    'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères',
                ]),
            ],
            'attr' => [
                'placeholder' => '{{app.user.firstname}}',
            ],
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Votre nom',
            'constraints' => [
                new Length([
                    'min' => 2,
                    'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères',
                ]),
            ],
            'attr' => [
                'placeholder' => '{{app.user.lastname}}',
            ],
        ])

        ->add('submit', SubmitType::class, [
            'label' => "Modifier Nom et Prénom",
            'attr' => [
                'class' => 'btn btn-dark w-100 mt-2',
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
