<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\EventDate;
use App\Service\AppServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EventDateTypeMulti extends AbstractType
{
    private $services;
    private $user;

    public function __construct(AppServices $services, TokenStorageInterface $tokenStorage)
    {
        $this->services = $services;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startdate', DateTimeType::class, [
                'required' => true,
                'label' => 'Start Date and Time',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datetimepicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'Select the start date and time (e.g., yyyy-MM-dd HH:mm)',
                    'title' => 'Select the start date and time of the event.',
                ],
                'format' => 'yyyy-MM-dd HH:mm',
            ])
            ->add('enddate', DateTimeType::class, [
                'required' => false,
                'label' => 'End Date and Time',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datetimepicker',
                    'autocomplete' => 'off',
                    'placeholder' => 'Select the end date and time (e.g., yyyy-MM-dd HH:mm)',
                    'title' => 'Select the end date and time of the event.',
                ],
                'format' => 'yyyy-MM-dd HH:mm',
            ])
            ->add('tickets', CollectionType::class, [
                'label' => 'Event Ticket',
                'entry_type' => EventTicketType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__eventticket__',
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-collection eventtickets-collection manual-init',
                    'placeholder' => 'Add an event ticket here',
                    'title' => 'Add an event ticket for this event date.',
                ],
            ])
            ->add('reference', HiddenType::class, [
                'attr' => [
                    'class' => 'event-date-reference',
                ],
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventDate::class,
            'validation_groups' => ['create', 'update'],
        ]);
    }
}
