<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\FacilityEntity;
use App\Service\AmenityService;
use App\Validator\Constraints\TagLimitConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FacilityType extends AbstractType
{
    private $amenityService;

    public function __construct(AmenityService $amenityService)
    {
        $this->amenityService = $amenityService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'purify_html' => true,
                'label' => 'Facility Name',
                'attr' => [
                    'placeholder' => 'Enter the name of the facility',
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => 'Facility Description',
                'attr' => [
                    'placeholder' => 'Provide a description of the facility',
                    'class' => 'wysiwyg'
                ],
            ])
            ->add('duration', ChoiceType::class, [
                'required' => true,
                'label' => 'Maximum Duration of a session',
                'choices' => [
                    '30 minutes' => 30,
                    '60 minutes' => 60,
                    '90 minutes' => 90,
                    '120 minutes' => 120,
                ],
                'attr' => [
                    'class' => 'select-input', // You can add a custom class for styling
                ],
            ])
            ->add('minPlayers', ChoiceType::class, [
                'required' => true,
                'label' => 'Minimum number of players per session',
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10,
                    '11' => 11,
                    '12' => 12,
                ],
                'attr' => [
                    'class' => 'select-input', // You can add a custom class for styling
                ],
            ])
            ->add('maxPlayers', ChoiceType::class, [
                'required' => true,
                'label' => 'Maximum number of players per session',
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10,
                    '11' => 11,
                    '12' => 12,
                ],
                'attr' => [
                    'class' => 'select-input', // You can add a custom class for styling
                ],
            ])
            ->add('tags', TextType::class, [
                'required' => false,
                'label' => 'Tags',
                'purify_html' => true,
                'help' => 'Enter tags and press Enter',
                'attr' => ['class' => 'tags-input'],
                'constraints' => [
                    new TagLimitConstraint(),
                ],
            ])
            ->add('mainImageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'label' => 'Main Athletic Facility image',
                'help' => 'Choose the main image that represents the facility (recommended size: 1200x600px, 2:1 ratio).',
                'data' => $options['data']->getMainImageFile(), // Pre-fill with the existing main image file name
            ])
            ->add('secondaryImages', CollectionType::class, array(
                'label' => 'Images gallery',
                'entry_type' => FacilityImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'form-collection',
                ),
                'help' => 'Add other images that represent your facility to be displayed as a gallery.',
                'error_bubbling' => false,
            ))
            ->add('monthlySchedule', CollectionType::class, [
                'label' => 'Monthly Schedules',
                'entry_type' => MonthlyScheduleType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__month__',
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-collection month-collection manual-init',
                ],
                'help' => 'Add monthly schedules for the facility.',
                'error_bubbling' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Facility',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
//
//        // In your form event listener or subscriber, handle the addition of MonthlySchedules dynamically
//        $builder->get('monthlySchedule')->addModelTransformer(new CallbackTransformer(
//            function ($data) {
//                // Transform the data if needed
//                return $data;
//            },
//            function ($data) {
//                // Transform the data if needed
//                // Use addMonthlySchedule for each item in the array
//                foreach ($data as $monthlySchedule) {
//                    $this->addMonthlySchedule($monthlySchedule);
//                }
//
//                return $this;
//            }
//        ));
//
//        // Add the callback constraint
//        $builder->addModelTransformer(new CallbackTransformer(
//            function ($data) {
//                // Transform the data if needed
//                return $data;
//            },
//            function ($data) {
//                // Transform the data if needed
//                return $data;
//            }
//        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FacilityEntity::class,
            'validation_groups' => ['create', 'update'],
            'constraints' => [
                new Callback([$this, 'validateMaxPlayers']),
            ],
        ]);
    }

    public function validateMaxPlayers($data, ExecutionContextInterface $context)
    {
        // Validate maxPlayers against minPlayers
        $minPlayers = $context->getRoot()->get('minPlayers')->getData();
        $maxPlayers = $data;

        if ($maxPlayers < $minPlayers) {
            $context->buildViolation('Maximum players must be equal to or greater than minimum players.')
                ->atPath('maxPlayers')
                ->addViolation();
        }
    }
}
