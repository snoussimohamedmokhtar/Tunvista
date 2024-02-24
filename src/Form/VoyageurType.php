<?php

namespace App\Form;

use App\Entity\Voyageur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;


class VoyageurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NumPass', TextType::class, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Le champ NumPass doit contenir uniquement des chiffres.'
                    ])
                ]
            ])

            ->add('Nom', TextType::class) 
            ->add('Prenom', TextType::class)
            ->add('Age',TextType::class, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Le champ NumPass doit contenir uniquement des chiffres.'
                    ])
                ]
            ])
            ->add('EtatCivil', ChoiceType::class, [
                'choices' => [
                    'Single' => 'Single',
                    'Married' => 'Married',
                    'Divorced' => 'Divorced',
                    'Widowed' => 'Widowed',
                ],
            ]);            // ->add('voyage')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voyageur::class,
        ]);
    }
}
