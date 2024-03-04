<?php

namespace App\Form;

use App\Entity\Voyage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class VoyageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Programme', TextareaType::class, [
                'attr' => ['rows' => 3], // Adjust rows as needed
            ])
            ->add('DateDepart', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('DateArrive', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('Destination', TextareaType::class, [
                'attr' => ['rows' => 3],
            ])

            ->add('image',FileType::class,[
                'label' => 'image',
                'mapped' => false,
                'required' => false,

            ])
            ->add('Prix')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voyage::class,
        ]);
    }
}
