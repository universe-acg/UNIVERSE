<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\MonthlySchedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MonthlyScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'required' => true,
                'label' => 'Start Date and Time',
                'html5' => false,
                'format' => 'yyyy-MM-dd HH:mm',
            ])
            ->add('endDate', DateType::class, [
                'required' => true,
                'label' => 'End Date and Time',
                'html5' => false,
                'format' => 'yyyy-MM-dd HH:mm',
            ])
            ->add('operatingHours', CollectionType::class, [
                'label' => 'Operating Hours',
                'entry_type' => OperatingHoursType::class,
                'allow_add' => true,
                'prototype_name' => '__hour__',
                'allow_delete' => true,
                'prototype' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-collection hour-collection manual-init',
                ],
                'error_bubbling' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MonthlySchedule::class,
        ]);
    }
}
