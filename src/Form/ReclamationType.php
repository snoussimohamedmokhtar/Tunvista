<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('iduser')
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                // Add custom validation constraint
                'constraints' => [
                    new Callback([
                        'callback' => [$this, 'validateDescription'],
                    ]),
                ],
            ])
            ->add('date')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'technique' => 'technique',
                    'Rh' => 'Rh',
                    'bug ou glitch' => 'bug ou glitch'
                ],
                'placeholder' => 'Choose a type',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a type.',
                    ]),
                ],
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


    // Custom validation callback method
    public function validateDescription($description, ExecutionContextInterface $context)
    {
        // Perform custom validation logic
        if (preg_match('/\d/', $description)) {
            $context->buildViolation('The description cannot contain numbers.')
                ->addViolation();
        }
    }
}
