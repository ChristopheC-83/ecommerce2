<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgotPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Votre adresse email',
            'attr' => [
                'placeholder' => 'Entrez votre adresse email',
            ],
            'help'=> 'Vous recevrez un email pour réinitialiser votre mot de passe'
        ])

        ->add('submit', SubmitType::class, [
            'label' => "Réinitialiser le mot de passe",
            'attr' => [
                'class' => 'btn btn-dark w-100 mt-2',
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
