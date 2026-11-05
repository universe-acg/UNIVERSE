<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditAdminRegistrationTypeAttendee extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Set the entity class for your user
            'user' => null, // Pass the user object when creating the form
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                'constraints' => [
                    new NotBlank(),
                ],
                'data' => $options['user'] ? $options['user']->getUsername() : ''
            ])
            ->add('specialuser', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Is this a special user (special users have easter egg badge)?',
                'choices' => ['Yes' => true, 'No' => false],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'placeholder' => false,
                'attr' => [
                    'class' => 'custom-switch-input', // You can add custom CSS classes if needed
                ],
            ])
            ->add('email', EmailType::class, [
                'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
//                    new AcgEmailDomain([
//                        'message' => 'Invalid email domain. Please use an @acg.edu email address.',
//                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Enter Email',
                    'title' => 'Enter your email address.',
                ],
                'data' => $options['user'] ? $options['user']->getEmail() : ''
            ]);
    }

}
