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
                'purify_html' => true,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'First name'
                )
            ])
            ->add('lastname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Last name'
                )
            ])
            ->add('username', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Username'
                )
            ])
            ->add('email', EmailType::class, [
                'purify_html' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Email',
                ],
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
            ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array(
                    'purify_html' => true,
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'placeholder' => 'Password',
                        'autocomplete' => 'new-password',
                    ),
                ),
                'first_options' => array('attr' => array('placeholder' => 'form.password')),
                'second_options' => array('attr' => array('placeholder' => 'form.password_confirmation')),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
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
