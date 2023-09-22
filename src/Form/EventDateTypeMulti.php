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
//                ->add('active', ChoiceType::class, [
//                    'required' => true,
//                    'multiple' => false,
//                    'expanded' => true,
//                    'label' => 'Enable sales for this event date ?',
//                    'choices' => ['Yes' => true, 'No' => false],
//                    'attr' => ['class' => 'is-event-date-active'],
//                    'label_attr' => ['class' => 'radio-custom radio-inline'],
//                    'help' => 'Enabling sales for an event date does not affect the tickets individual sale status'
//                ])
            ->add('startdate', DateTimeType::class, [
                'required' => true,
                'label' => 'Starts On',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datetimepicker', 'autocomplete' => 'off'],
                'format' => 'yyyy-MM-dd HH:mm',
            ])
            ->add('enddate', DateTimeType::class, [
                'required' => true,
                'label' => 'Ends On',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datetimepicker', 'autocomplete' => 'off'],
                'format' => 'yyyy-MM-dd HH:mm',
            ])
//                ->add('online', ChoiceType::class, [
//                    'required' => true,
//                    'multiple' => false,
//                    'expanded' => true,
//                    'label' => 'Is this an online event?',
//                    'choices' => ['No' => false, 'Yes' => true],
//                    'attr' => ['class' => 'is-event-date-online'],
//                    'label_attr' => ['class' => 'radio-custom radio-inline']
//                ])
//                ->add('venue', EntityType::class, [
//                    'required' => false,
//                    'class' => Venue::class,
//                    'choice_label' => 'name',
//                    'label' => 'Venue',
//                    'attr' => ['class' => 'event-date-venue'],
//                    'query_builder' => function () {
//                        return $this->services->getVenues(array("organizer" => $this->user->getOrganizer()->getSlug()));
//                    },
//                ])
//            ->add('scanners', EntityType::class, [
//                'required' => false,
//                'disabled' => true,
//                'multiple' => true,
//                'expanded' => false,
//                'class' => Scanner::class,
//                'choice_label' => 'name',
//                'label' => 'Scanners',
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('s')
//                        ->where('s.organizer = :organizer')
//                        ->leftJoin('s.user', 'user')
//                        ->andWhere('user.enabled = :enabled')
//                        ->setParameter('organizer', $this->user->getOrganizer())
//                        ->setParameter('enabled', true);
//                },
//                'attr' => ['class' => 'select2']
//            ])
//                ->add('pointofsales', EntityType::class, [
//                    'required' => false,
//                    'disabled' => true,
//                    'multiple' => true,
//                    'expanded' => false,
//                    'class' => PointOfSale::class,
//                    'choice_label' => 'name',
//                    'label' => 'Points of sale',
//                    'query_builder' => function (EntityRepository $er) {
//                        return $er->createQueryBuilder('p')
//                            ->where('p.organizer = :organizer')
//                            ->leftJoin('p.user', 'user')
//                            ->andWhere('user.enabled = :enabled')
//                            ->setParameter('organizer', $this->user->getOrganizer())
//                            ->setParameter('enabled', true);
//                    },
//                    'attr' => ['class' => 'select2']
//                ])
            ->add('tickets', CollectionType::class, array(
                'label' => 'Event tickets',
                'entry_type' => EventTicketTypeMulti::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__eventticket__',
                'required' => true,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'form-collection eventtickets-collection manual-init',
                ),
            ))
            // Set automatically on entity creation (generation function on entity class),
            // added here as a trick to identity the event date on the form to disable the wrapping
            // fieldset when payout request is pending on approved
            ->add('reference', HiddenType::class, [
                'attr' => [
                    'class' => 'event-date-reference']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventDate::class,
            'validation_groups' => ['create', 'update']
        ]);
    }
}