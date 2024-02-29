<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_debut', null, [
                'constraints' => [
                    new NotBlank(['message' => 'La date ne doit pas être vide.']),
                ],
            ])
            ->add('type_a', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Le type ne doit pas être vide.']),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\']+$/',
                        'message' => 'Le type ne doit contenir que des lettres.'
                    ]),
                ],
            ])
            ->add('user', null, [
                'constraints' => [
                    new NotBlank(['message' => 'User ne doit pas être vide.']),
                ],
            ]);

            /*->add('picture', type: FileType::class, options: [
                'label' => 'Picture :',
                'mapped' => false,
                'required' => false,
            ]);*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
