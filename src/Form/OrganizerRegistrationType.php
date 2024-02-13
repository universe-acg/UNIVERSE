<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Organizer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class OrganizerRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter the creator name',
                    'help' => 'Provide the name of your organization or group.',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Organizer Name',
            ])
            ->add('category', ChoiceType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                ],
                'choices' => [
                    'Club' => 'Club',
                    'Office' => 'Office',
                    'Society' => 'Society',
                    'Organization' => 'Organization',
                ],
                'placeholder' => 'Select a category',
                'attr' => [
                    'help' => 'Choose the category that best describes your organization.',
                ],
                'label' => 'Category',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organizer::class,
        ]);
    }
}
