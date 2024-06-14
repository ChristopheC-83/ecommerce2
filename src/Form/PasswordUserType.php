<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'placeholder' => 'Indiquez votre mot de passe actuel',
                ],
                'mapped' => false,   //on ne veut rien envoyer en DB, juste comparer. mapped=false => ne fais pas le lien !
            ])


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

            // Pour vérifier le mdp actuel, on va écouter l'événement SUBMIT
            // récupérer les données du formulaire
            // comparer le mdp actuel avec le mdp en DB
            //  le faire ici allège le controller

            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                // dd($form->getConfig()->getOptions()['data']);
                $form = $event->getForm();

                dd($form);
                $user = ($form->getConfig()->getOptions()['data']);
                $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];


                // on récupère les données du formulaire et comparaison avec celui en DB
                $isValid = $passwordHasher->isPasswordValid($user, $form->get('actualPassword')->getData());

                // dd($isValid);
    
                if (!$isValid) {
                    $form->get('actualPassword')->addError(new FormError('Le mot de passe est incorrect, Merci de vérifier votre saisie.'));

                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null,
        ]);
    }
}
