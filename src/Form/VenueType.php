<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Amenity;
use App\Entity\Venue;
use App\Service\AmenityService;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class VenueType extends AbstractType
{
    private $amenityService;

    public function __construct(AmenityService $amenityService)
    {
        $this->amenityService = $amenityService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
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
            ->add('mainImageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'label' => 'Main venue image',
                'help' => 'Choose the main image that represents the venue (recommended size: 1200x600px, 2:1 ratio).',
                'data' => $options['data']->getMainImageFile(), // Pre-fill with the existing main image file name
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
                    return $this->amenityService->getAmenitiessql([]);
                }
            ])
            ->add('secondaryImages', CollectionType::class, array(
                'label' => 'Images gallery',
                'entry_type' => VenueImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'form-collection',
                ),
                'help' => 'Add other images that represent your venue to be displayed as a gallery.',
                'error_bubbling' => false,
            ))
            ->add('save', SubmitType::class, [
                'label' => 'Save Venue',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Venue::class,
            'validation_groups' => ['create', 'update'],
        ]);

        $resolver->setDefined('user_choices'); // Define a custom option for user choices

        $resolver->setDefault('choices', function (Options $options) {
            // Return the choices as an array
            return $options['user_choices'];
        });
    }
}
