<?php

namespace App\Form;

use App\Entity\Visit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class VisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            


        ->add('dateVisit', DateType::class, [
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'data' => new \DateTime(),
            'constraints' => [
                new GreaterThan("today")
            ]
        ])
        ->add('Numero', null, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'numeric']),
                new Assert\Length(['max' => 255,
                'message' => 'Le numero doit contenir 8 chiffre.']),
            ],
        ])
        ->add('nom', null, [
            'required' => false,
            'constraints' => [
                new NotBlank([
                    
                    'message' => 'Veuillez saisir un nom.'
                ]), 
                new Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le nom doit contenir uniquement des lettres.']),           
            ],  
        ])
            ->add('email', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            
            ->add('refB', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Visit::class,
        ]);
    }
}
