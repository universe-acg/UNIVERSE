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
                    'placeholder' => 'Enter their first name',
                ],
                'help' => 'Please enter their first name.',
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'label' => 'Last Name',
                'attr' => [
                    'placeholder' => 'Enter their last name',
                ],
                'help' => 'Please enter their last name.',
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
                    'placeholder' => 'Enter their email address',
                ],
                'help' => 'Please enter their email address for communication.',
            ])
            ->add('isbusinessweek', ChoiceType::class, [
                'label' => 'Are they a multi-ticket user?',
                'required' => true,
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'expanded' => true, // Display as radio buttons
                'data' => false, // Set the default value based on the user's status
                'attr' => [
                    'help' => 'Select "Yes" if they are a multi-ticket user, otherwise select "No".',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'purify_html' => true,
                ],
                'invalid_message' => 'fos_user.password.mismatch',
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
