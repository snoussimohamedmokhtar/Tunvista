<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('iduser')
            ->add('description')
            ->add('date')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'technique' => 'technique',
                    'Rh' => 'Rh',
                    'bug ou glitch' => 'bug ou glitch'
                ],
                'placeholder' => 'Choose a type',
                'required' => true
            ])
            ->add('etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
