<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminRegistrationOrganizerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => 'First Name',
                'attr' => [
                    'placeholder' => 'Enter your first name',
                ],
                'help' => 'Please enter your first name.',
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'label' => 'Last Name',
                'attr' => [
                    'placeholder' => 'Enter your last name',
                ],
                'help' => 'Please enter your last name.',
            ])
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Username',
                'attr' => [
                    'placeholder' => 'Choose a username',
                ],
                'help' => 'Choose a unique username.',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Enter your email address',
                ],
                'help' => 'Please enter your email address for communication.',
            ])
            ->add('isbusinessweek', ChoiceType::class, [
                'label' => 'Are you a multi-ticket user?',
                'required' => true,
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'expanded' => true, // Display as radio buttons
                'data' => false, // Set the default value based on the user's status
                'attr' => [
                    'help' => 'Select "Yes" if you are a multi-ticket user, otherwise select "No".',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'translation_domain' => 'FOSUserBundle',
                ],
                'invalid_message' => 'Passwords do not match.',
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Enter your password',
                    ],
                    'help' => 'Please enter your password.',
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Confirm your password',
                    ],
                    'help' => 'Please confirm your password.',
                ],
            ])
            ->add('organizer', OrganizerRegistrationType::class);
    }

    public function getParent()
    {
        return RegistrationFormType::class;
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
}
