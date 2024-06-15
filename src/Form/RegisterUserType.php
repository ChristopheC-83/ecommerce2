<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserType extends AbstractType
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
                    'placeholder' => 'Indiquez votre prénom',
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
                    'placeholder' => 'Indiquez votre nom',
                ],
            ])

            // pas de contrainte d'unicité sur ici mais sur l'entité globale, fonction suivante de ce fichier
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'attr' => [
                    'placeholder' => 'Entrez votre adresse email',
                ],
            ])

            //  on ne veut pas que l'user qui s'inscrit puisse choisir son role
            // ->add('roles')

            // si on demande le mot de passe sans vérification
            // ->add('password', PasswordType::class, [
            //     'label' => 'Votre mot de passe',
            //     'attr' => [
            //         'placeholder' => 'Choisissez votre mot de passe',
            //     ],
            // ])

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
                    'label' => 'Votre mot de passe', 
                    'hash_property_path' => 'password',
                    'attr' => [
                    'placeholder' => 'Entrez votre mot de passe',
                ],
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                    'placeholder' => 'Entrez une seconde fois votre mot de passe',
                ],
                ],
                // on explique à Symfo qu'il n'y a pas de lien entre ce formulaire et l'entity lié au formulaire
                // on récupere 'password' dans 'hash_property_path'
                'mapped' => false,  
            ])


            ->add('submit', SubmitType::class, [
                'label' => "M'inscrire",
                'attr' => [
                    'class' => 'btn btn-dark w-100 mt-2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

            //  ici contrainte globale sur l'entité User
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email',
                    'message' => 'Cette adresse email est déjà utilisée',
                ]),
            ],
        ]);
    }
}
