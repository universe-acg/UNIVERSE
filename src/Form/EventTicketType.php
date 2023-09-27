<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\EventTicket;
use App\Service\AppServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTicketType extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Total Available Tickets',
                'attr' => [
                    'class' => 'touchspin-integer event-date-ticket-quantity',
                    'data-min' => '1',
                    'data-max' => '1000000',
                    'placeholder' => 'Enter the total number of tickets available',
                ],
                'help' => 'Specify the total number of tickets available for this ticket type.',
            ])
            ->add('salesstartdate', DateTimeType::class, [
                'required' => true,
                'label' => 'Registration Starts On',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datetimepicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'Select the start date and time',
                ],
                'format' => 'yyyy-MM-dd HH:mm',
                'help' => 'Choose the date and time when registration for this ticket type starts.',
            ])
            ->add('salesenddate', DateTimeType::class, [
                'required' => true,
                'label' => 'Registration Ends On',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datetimepicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'Select the end date and time',
                ],
                'format' => 'yyyy-MM-dd HH:mm',
                'help' => 'Choose the date and time when registration for this ticket type ends.',
            ])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'class' => 'event-date-ticket-position',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventTicket::class,
            'validation_groups' => ['create', 'update'],
        ]);
    }
}
