<?php
namespace App\Form;

use App\Entity\Feedback;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('message', TextareaType::class, [
'label' => 'Feedback',
'attr' => ['placeholder' => 'Feedback'],
]);
}

public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
// Configure your form options here
]);
}
}
