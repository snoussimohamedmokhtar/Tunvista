<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class Location1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_debut', null, [
                'constraints' => [
                    new NotBlank(),
                    new Type(\DateTime::class),
                ],
            ])
            ->add('date_fin', null, [
                'constraints' => [
                    new NotBlank(),
                    new Type(\DateTime::class),
                ],
            ])
            ->add('client', null, [
                'constraints' => [
                    new NotBlank(),

                ],
            ])
            ->add('voiture', null, [
                'constraints' => [
                    new NotBlank(),

                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
