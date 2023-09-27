<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Your Rating (out of 5 stars)',
                'choices' => ['5 stars' => 5, '4 stars' => 4, '3 stars' => 3, '2 stars' => 2, '1 star' => 1],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Select your rating on a scale of 1 to 5 stars.',
            ])
            ->add('headline', TextType::class, [
                'required' => true,
                'label' => 'Review Headline',
                'attr' => [
                    'placeholder' => 'Enter a headline for your review',
                    'help' => 'Write a brief headline summarizing your review.',
                ],
            ])
            ->add('details', TextareaType::class, [
                'required' => true,
                'label' => 'Review Details',
                'attr' => [
                    'placeholder' => 'Share your detailed experience...',
                    'rows' => 10,
                    'help' => 'Provide a detailed review of your experience, including any relevant information.',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Review',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
