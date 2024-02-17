<?php

namespace App\Form;

use App\Entity\Maison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class MaisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('adresse', ChoiceType::class, [
                'choices' => [
                    'Ariana' => 'Ariana',
                    'Béja' => 'Béja',
                    'Ben Arous' => 'Ben Arous',
                    'Bizerte'=>'Bizerte',
                    'Gabès'=>'Gabès',
                    'Gafsa'=>'Gafsa',
                    'Jendouba'=>'Jendouba',
                    'Kairouan'=>'Kairouan',
                    'Kasserine'=>'Kasserine',
                    'Kébili'=>'Kébili',
                    'Le Kef'=>'Le Kef',
                    'Mahdia'=>'Mahdia',
                    'La Manouba'=>'La Manouba',
                    'Médenine'=>'Médenine',
                    'Monastir'=>'Monastir',
                    'Nabeul'=>'Nabeul',
                    'Sfax'=>'Sfax',
                    'Sidi Bouzid'=>'Sidi Bouzid',
                    'Siliana'=>'Siliana',
                    'Sousse'=>'Sousse',
                    'Tataouine'=>'Tataouine',
                    'Tozeur'=>'Tozeur',
                    'Tunis' => 'Tunis',
                    'Zaghouan'=>'Zaghouan',

                ]])

            ->add('nombreChambre', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                ],
            ])
            ->add('prix', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                ],
            ])

            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Villa' => 'Villa',
                    'Appartement' => 'Appartement',
                    'Studio' => 'Studio',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Maison::class,
        ]);
    }
}
