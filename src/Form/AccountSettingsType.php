<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\User;
use App\Service\AppServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email as SymfonyEmail;
use Symfony\Component\Validator\Constraints\Length;

class AccountSettingsType extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Email',
                'help' => 'Enter your email address',
                'constraints' => [
                    new SymfonyEmail([
                        'message' => 'Please enter a valid email address.'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 30,
                        'minMessage' => 'Email should be at least {{ limit }} characters long.',
                        'maxMessage' => 'Email should be no more than {{ limit }} characters long.'
                    ])
                ],
                'attr' => [
                    'readonly' => true,
                    'placeholder' => 'example@example.com',
                ]
            ])
            ->add('username', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Username',
                'help' => 'Enter your username',
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 30,
                        'minMessage' => 'Username should be at least {{ limit }} characters long.',
                        'maxMessage' => 'Username should be no more than {{ limit }} characters long.'
                    ])
                ],
                'attr' => [
                    'readonly' => true,
                    'placeholder' => 'john_doe123',
                ]
            ])
            ->add('firstname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'First name',
                'help' => 'Enter your first name',
                'attr' => [
                    'placeholder' => 'John',
                ]
            ])
            ->add('lastname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Last name',
                'help' => 'Enter your last name',
                'attr' => [
                    'placeholder' => 'Doe',
                ]
            ])
            ->add('phone', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Phone number',
                'help' => 'Enter your phone number (optional)',
                'attr' => [
                    'placeholder' => '123-456-7890',
                ]
            ])
            ->add('birthdate', DateType::class, [
                'required' => false,
                'label' => 'Birthdate',
                'help' => 'Select your birthdate (optional)',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'placeholder' => 'YYYY-MM-DD',
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
