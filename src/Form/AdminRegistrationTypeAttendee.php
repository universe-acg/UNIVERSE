<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Validator\Constraints\AcgEmailDomain;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AdminRegistrationTypeAttendee extends AbstractType
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getCurrentRequest();
        $currentRoute = $request->attributes->get('_route');

        $builder
            ->add('firstname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'First Name',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => ['placeholder' => 'Enter their first name'],
            ])
            ->add('lastname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Last Name',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => ['placeholder' => 'Enter their last name'],
            ]);

        if ($currentRoute === 'registration_register_attendee_admin') {
            $builder->
            add('studentid', TextType::class, [
                'required' => true,
                'label' => 'Student ID',
                'attr' => [
                    'placeholder' => 'Enter your ACG Student ID',
                    'help' => 'Enter your email address.',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
        }

        $builder->add('username', TextType::class, [
            'purify_html' => true,
            'required' => true,
            'label' => 'Username',
            'constraints' => [
                new NotBlank(),
            ],
            'attr' => ['placeholder' => 'Choose a username'],
        ])
            ->add('email', EmailType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new AcgEmailDomain([
                        'message' => 'Invalid email domain. Please use an @acg.edu email address.',
                    ]),
                ],
                'attr' => ['placeholder' => 'Enter their email address'],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'purify_html' => true,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password.',
                    ]),
                    new Length([
                        'min' => 8, // Minimum password length
                        'minMessage' => 'Your password should be at least {{ limit }} characters long.',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&_])[A-Za-z\d@$!%*?&_]+$/',
                        'message' => 'Your password must contain at least one uppercase letter, one lowercase letter, one number, and one special character from these @$!%*?&._',
                    ]),
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
