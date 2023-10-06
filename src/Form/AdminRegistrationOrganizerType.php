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
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Regex;
    
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
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'help' => 'Please enter their first name.',
                ])
                ->add('lastname', TextType::class, [
                    'required' => true,
                    'label' => 'Last Name',
                    'attr' => [
                        'placeholder' => 'Enter their last name',
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'help' => 'Please enter their last name.',
                ])
                ->add('username', TextType::class, [
                    'required' => true,
                    'label' => 'Username',
                    'attr' => [
                        'placeholder' => 'Choose a username',
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'help' => 'Choose a unique username.',
                ])
                ->add('email', EmailType::class, [
                    'required' => true,
                    'label' => 'Email',
                    'attr' => [
                        'placeholder' => 'Enter their email address',
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new AcgEmailDomain([
                            'message' => 'Invalid email domain. Please use an @acg.edu email address.',
                        ]),],
                    'help' => 'Please enter their email address for communication.',
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
                            'minMessage' => 'Your password should be at least 8 characters long.',
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                            'message' => 'Your password must contain at least one uppercase letter, one lowercase letter, one number, and one special character from these @$!%*?&.',
                        ]),
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
