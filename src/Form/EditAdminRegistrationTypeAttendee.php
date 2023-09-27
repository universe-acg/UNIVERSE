<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\User;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAdminRegistrationTypeAttendee extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Set the entity class for your user
            'user' => null, // Pass the user object when creating the form
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter First Name',
                    'title' => 'Enter your first name.',
                ],
                'data' => $options['user'] ? $options['user']->getFirstname() : ''
            ])
            ->add('lastname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter Last Name',
                    'title' => 'Enter your last name.',
                ],
                'data' => $options['user'] ? $options['user']->getLastname() : ''
            ])
            ->add('username', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Choose a Username',
                    'title' => 'Choose a unique username.',
                ],
                'data' => $options['user'] ? $options['user']->getUsername() : ''
            ])
            ->add('email', EmailType::class, [
                'purify_html' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter Email',
                    'title' => 'Enter your email address.',
                ],
                'data' => $options['user'] ? $options['user']->getEmail() : ''
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
