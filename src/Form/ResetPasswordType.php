<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Votre mot de passe peut contenir au maximum {{ limit }} caractères',
                    ]),
                ],
                'first_options' => [
                    'label' => 'Votre nouveau mot de passe',
                    'hash_property_path' => 'password',  // ce password correspond à password dans l'entité User
                    'attr' => [
                        'placeholder' => 'Entrez votre nouveau mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmez votre nouveau nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Entrez une seconde fois votre nouveau mot de passe',
                    ],
                ],
                // on explique à Symfo qu'il n'y a pas de lien entre ce formulaire et l'entity lié au formulaire
                // on récupere 'password' dans 'hash_property_path'
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Mettre à jour mon mot de passe",
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
