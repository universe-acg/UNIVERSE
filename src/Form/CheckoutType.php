<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Service\AppServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutType extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('orderReference', HiddenType::class, [
//                'required' => false,
//                'constraints' => array(
//                    new NotBlank(['groups' => ['attendee', 'pos']])
//                ),
//            ]);
//            ->add('firstname', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'First name',
//                'constraints' => array(
//                    new NotBlank(['groups' => ['attendee']]),
//                    new Length([
//                        'min' => 2,
//                        'max' => 20,
//                        'groups' => ['attendee', 'pos']])
//                ),
//            ])
//            ->add('lastname', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Last name',
//                'constraints' => array(
//                    new NotBlank(['groups' => ['attendee']]),
//                    new Length([
//                        'min' => 2,
//                        'max' => 20,
//                        'groups' => ['attendee', 'pos']])
//                ),
//            ])
//            ->add('email', EmailType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Email',
//                'constraints' => array(
//                    new Assert\Email(['groups' => ['attendee']]),
//                    new NotBlank(['groups' => ['attendee']]),
//                    new Length([
//                        'min' => 2,
//                        'max' => 50,
//                        'groups' => ['attendee']])
//                ),
//            ])
//            ->add('country', EntityType::class, [
//                'required' => false,
//                'class' => Country::class,
//                'choice_label' => 'name',
//                'label' => 'Country',
//                'attr' => ['class' => 'select2'],
//                'placeholder' => 'Select an option',
//            ])
//            ->add('state', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'State',
//                'constraints' => array(
//                    new NotBlank(['groups' => ['attendee']]),
//                    new Length([
//                        'min' => 2,
//                        'max' => 50,
//                        'groups' => ['attendee']])
//                ),
//            ])
//            ->add('city', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'City',
//                'constraints' => array(
//                    new NotBlank(['groups' => ['attendee']]),
//                    new Length([
//                        'min' => 2,
//                        'max' => 50,
//                        'groups' => ['attendee']])
//                ),
//            ])
//            ->add('postalcode', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Postal code',
//                'constraints' => array(
//                    new NotBlank(['groups' => ['attendee']]),
//                    new Length([
//                        'min' => 2,
//                        'max' => 15,
//                        'groups' => ['attendee']])
//                ),
//            ])
//            ->add('street', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Street',
//                'constraints' => array(
//                    new NotBlank(['groups' => ['attendee']]),
//                    new Length([
//                        'min' => 2,
//                        'max' => 50,
//                        'groups' => ['attendee']])
//                ),
//            ])
//            ->add('street2', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Street 2',
//                'constraints' => array(
//                    new Length([
//                        'max' => 50,
//                        'groups' => ['attendee']
//                    ])
//                ),
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
//            'validation_groups' => ['attendee', 'pos']
        ]);
    }

}
