<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Form;
    
    use App\Validator\Constraints\AcgEmailDomain;
    use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
    use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
    use FOS\UserBundle\Form\Type\RegistrationFormType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Regex;
    
    class RegistrationType extends AbstractType
    {
        
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('firstname', TextType::class, [
                    'required' => true,
                    'label' => 'First Name',
                    'attr' => [
                        'placeholder' => 'Enter your first name',
                        'help' => 'Enter your first name.',
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
                ->add('lastname', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'label' => 'Last Name',
                    'attr' => [
                        'placeholder' => 'Enter your last name',
                        'help' => 'Enter your last name.',
                    ],
                ])
                ->add('username', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'required' => true,
                    'label' => 'Username',
                    'attr' => [
                        'placeholder' => 'Choose a username',
                        'help' => 'Choose a unique username.',
                    ],
                ])
                ->add('email', EmailType::class, [
                    'required' => true,
                    'label' => 'Email',
                    'attr' => [
                        'placeholder' => 'Enter your email address',
                        'help' => 'Enter your email address.',
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new AcgEmailDomain([
                            'message' => 'Invalid email domain. Please use an @acg.edu email address.',
                        ]),
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'options' => array(
                        'attr' => [
                            'placeholder' => 'Choose a password',
                            'autocomplete' => 'new-password',
                            'help' => 'Choose a strong password. It must contain at least 8 characters, one uppercase letter, one lowercase letter, one number, and one special character.',
                        ],
                    ),
                    'first_options' => array(
                        'label' => 'Password',
                    ),
                    'second_options' => array(
                        'label' => 'Repeat Password',
                    ),
                    'invalid_message' => 'Passwords do not match.',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password.',
                        ]),
                        new Length([
                            'min' => 8, // Minimum password length
                            'minMessage' => 'Your password should be at least {{ limit }} characters long.',
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                            'message' => 'Your password must contain at least one uppercase letter, one lowercase letter, one number, and one special character from these @$!%*?&.',
                        ]),
                    ],
                ))
                ->add('organizer', OrganizerRegistrationType::class)
                ->add('recaptcha', EWZRecaptchaType::class, array(
                    'attr' => array(
                        'options' => array(
                            'theme' => 'light',
                            'type' => 'image',
                            'size' => 'normal',
                        )
                    ),
                    'mapped' => false,
                    'constraints' => array(
                        new RecaptchaTrue(['groups' => 'Registration'])
                    )
                ));
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
