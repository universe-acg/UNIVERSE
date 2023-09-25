<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminRegistrationTypeAttendee extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'First Name',
                'attr' => ['placeholder' => 'Enter your first name'],
            ])
            ->add('lastname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Last Name',
                'attr' => ['placeholder' => 'Enter your last name'],
            ])
            ->add('username', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Username',
                'attr' => ['placeholder' => 'Choose a username'],
            ])
            ->add('email', EmailType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Email',
                'attr' => ['placeholder' => 'Enter your email address'],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'purify_html' => true,
                    'translation_domain' => 'FOSUserBundle',
                ],
                'invalid_message' => 'fos_user.password.mismatch',
            ]);
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
