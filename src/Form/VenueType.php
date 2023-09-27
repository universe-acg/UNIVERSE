<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Amenity;
use App\Entity\Venue;
use App\Entity\VenueType as Type;
use App\Service\AppServices;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VenueType extends AbstractType
{
    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'purify_html' => true,
                'label' => 'Venue Name',
                'attr' => [
                    'placeholder' => 'Enter the name of the venue',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Venue Description',
                'attr' => [
                    'placeholder' => 'Provide a description of the venue',
                    'class' => 'wysiwyg'
                ],
            ])
            ->add('type', EntityType::class, [
                'required' => true,
                'class' => Type::class,
                'choice_label' => 'name',
                'label' => 'Venue Type',
                'placeholder' => 'Select a venue type',
                'query_builder' => function () {
                    return $this->services->getVenuesTypes(array());
                }
            ])
            ->add('amenities', EntityType::class, [
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'class' => Amenity::class,
                'choice_label' => 'name',
                'label' => 'Amenities',
                'label_attr' => ['class' => 'checkbox-custom checkbox-inline'],
                'query_builder' => function () {
                    return $this->services->getAmenities(array());
                }
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Venue',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Venue::class,
        ]);
    }
}
