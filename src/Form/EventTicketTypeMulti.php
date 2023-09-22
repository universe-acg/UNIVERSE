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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTicketTypeMulti extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('active', ChoiceType::class, [
//                'required' => false,
//                'multiple' => false,
//                'expanded' => true,
//                'label' => 'Enable sales for this ticket ?',
//                'choices' => ['Yes' => true, 'No' => false],
//                'attr' => ['class' => 'is-ticket-active'],
//                'label_attr' => ['class' => 'radio-custom radio-inline']
//            ])
            ->add('name', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Ticket name',
                'help' => 'General admission'
            ])
            ->add('description', TextareaType::class, [
                'purify_html' => true,
                'required' => false,
                'disabled' => false,
                'label' => 'Ticket description',
                'help' => 'Tell your attendees more about this ticket type'
            ])
//            ->add('free', ChoiceType::class, [
//                'required' => false,
//                'multiple' => false,
//                'expanded' => true,
//                'label' => 'Is this ticket free ?',
//                'choices' => ['No' => false, 'Yes' => true],
//                'attr' => ['class' => 'is-ticket-free-radio'],
//                'label_attr' => ['class' => 'radio-custom radio-inline']
//            ])
//            ->add('price', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Price',
//                'attr' => ['class' => 'touchspin-decimal event-date-ticket-price', 'data-min' => '0', "data-max" => '100000000']
//            ])
//            ->add('promotionalprice', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Promotional price',
//                'help' => 'Set a price lesser than than the original price to indicate a promotion (this price will be the SALE price)',
//                'attr' => ['class' => 'touchspin-decimal event-date-ticket-promotionalprice', 'data-min' => '0', "data-max" => '100000000']
//            ])
            ->add('quantity', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Total amount of tickets available (per event date, in case the event is repeating)',
                'attr' => ['class' => 'touchspin-integer event-date-ticket-quantity', 'data-min' => '1', "data-max" => '1000000']
            ])
//            ->add('ticketsperattendee', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Tickets per attendee',
//                'help' => 'Set the number of tickets that an attendee can buy for this ticket type',
//                'attr' => ['class' => 'touchspin-integer', 'data-min' => '1', "data-max" => '1000000']
//            ])
            ->add('salesstartdate', DateTimeType::class, [
                'required' => true,
                'label' => 'Registration Starts on',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datetimepicker', 'autocomplete' => 'off'],
                'format' => 'yyyy-MM-dd HH:mm',
            ])
            ->add('salesenddate', DateTimeType::class, [
                'required' => true,
                'label' => 'Registration Ends on',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datetimepicker', 'autocomplete' => 'off'],
                'format' => 'yyyy-MM-dd HH:mm'])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'class' => 'event-date-ticket-position']])
            // Set automatically on entity creation (generation function on entity class),
            // added here as a trick to identity the event ticket
            // fieldset to set the data-min attribute as the current ticket sales number
            ->add('reference', HiddenType::class, [
                'attr' => [
                    'class' => 'event-date-ticket-reference']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventTicket::class,
            'validation_groups' => ['create', 'update']
        ]);
    }
}
