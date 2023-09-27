<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\VenueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VenueTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'purify_html' => true,
                'label' => 'Venue Type Name',
                'attr' => [
                    'placeholder' => 'Enter the name of the venue type',
                    'help' => 'Provide a unique name for the venue type.',
                ],
                'required' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Venue Type',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VenueType::class,
        ]);
    }
}
