<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\AthleticBooking;
use App\Service\FacilityService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AthleticBookingType extends AbstractType
{

    private $facilityService;

    public function __construct(FacilityService $facilityService)
    {
        $this->facilityService = $facilityService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('duration', ChoiceType::class, [
                'required' => true,
                'label' => 'Duration your reservation',
                'choices' => [
                    '30 minutes' => 30,
                    '60 minutes' => 60,
                    '90 minutes' => 90,
                    '120 minutes' => 120,
                ],
                'attr' => [
                    'class' => 'select-input bookdurationpicker', // You can add a custom class for styling
                ],
            ])
            ->add('bookingDate', DateTimeType::class, [
                'required' => true,
                'label' => 'Day of reservation',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'bookdatetimepicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'Select the start date and time (e.g., yyyy-MM-dd HH:mm)',
                    'title' => 'Select the start date and time of the event.',
                ],
                'format' => 'yyyy-MM-dd',
            ])
            ->add('bookingTime', ChoiceType::class, [
                'required' => true,
                'label' => 'Time of reservation',
                'choices' => $this->generateTimeChoices(),
                'attr' => [
                    'class' => 'mytimepicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'Select the time (e.g., HH:mm)',
                    'title' => 'Select the time of the event.',
                ],
            ])
            ->add('amount', ChoiceType::class, [
                'required' => true,
                'label' => 'Number of players of this reservation',
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
            ->add('status', ChoiceType::class, [
                'required' => true,
                'label' => 'Is this reservation open for others to join?',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'data' => false,
                'expanded' => true, // Display as radio buttons
                'multiple' => false, // Allow only one selection
                'label_attr' => [
                    'title' => 'If true, then others can join this reservation',
                    'class' => 'repeats-weekly-field radio-custom radio-inline',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Book',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    private function generateTimeChoices(): array
    {
        $choices = [];

        for ($hour = 0; $hour < 24; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 5) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $choices[$time] = $time;
            }
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AthleticBooking::class,
            'validation_groups' => ['create', 'update'],
        ]);
    }
}
