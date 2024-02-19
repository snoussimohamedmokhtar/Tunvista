<?php

namespace App\Form;

use App\Entity\Region;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'attr'=>[
                    'class' => 'form-control'
                ],
                'label'=>'E-mail',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email address',
                    ]),
                    new Email([
                        'message' => 'The email "{{ value }}" is not a valid email.',
                    ]),
                ]
            ])
            ->add('lastName',TextType::class,[
                'attr'=>[
                    'class' => 'form-control'
                    ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Your last name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Your last name cannot be longer than {{ limit }} characters',
                    ]),
                ]
                ])
            ->add('firstName',TextType::class,[
                'attr'=>[
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your fisrt name',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Your first name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Your first name cannot be longer than {{ limit }} characters',
                    ]),
                ]
            ])
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'placeholder' => 'Choisir une région', // Texte par défaut pour l'option de sélection
                'choice_label' => 'nom', // Remplacez 'nom' par le nom de la propriété de l'entité Region à afficher dans la liste déroulante
                'attr' => [
                    'class' => 'form-control' // Ajoutez des attributs de classe au champ si nécessaire
                ]
            ])
            ->add('ville',TextType::class,[
                'attr'=>[
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your city',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Your city name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Your city name cannot be longer than {{ limit }} characters',
                    ]),
                ]
            ])
            ->add('adresse',TextType::class,[
                'attr'=>[
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your adresse',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Your adresse name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Your adresse name cannot be longer than {{ limit }} characters',
                    ]),
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,

                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Regex('~^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$~',
                        "Password should be Minimum eight in length One Upper case, one lower case, one digit, one special character"

                    ),
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
