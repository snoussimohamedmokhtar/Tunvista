<?php

namespace App\Form;

use App\Entity\Evenement;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre_e', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Le type ne doit pas être vide.']),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\']+$/',
                        'message' => 'Le type ne doit contenir que des lettres.'
                    ]),
                ],
            ])
            ->add('description_e', null, [
                'constraints' => [
                    new NotBlank(['message' => 'User ne doit pas être vide.']),
                ],
            ])
            ->add('ville_e', null, [
                'constraints' => [
                    new NotBlank(['message' => 'User ne doit pas être vide.']),
                ],
            ])
            ->add('date_deb', null, [
                'constraints' => [
                    new NotBlank(['message' => 'La date ne doit pas être vide.']),
                ],
            ])

            /*->add('pictureE', type: FileType::class, options: [
                'label' => 'Picture :',
                'mapped' => false,
                'required' => false,
            ]);*/
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
