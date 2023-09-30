<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Form;
    
    use App\Entity\User;
    use App\Validator\Constraints\AcgEmailDomain;
    use FOS\UserBundle\Form\Type\RegistrationFormType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\NotNull;
    
    class EditAdminRegistrationOrganizerType extends AbstractType
    {
        
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('firstname', TextType::class, [
                    'purify_html' => true,
                    'required' => true, 'constraints' => [
                        new NotBlank(),
                    ],
                    'attr' => [
                        'placeholder' => 'Enter First Name',
                        'title' => 'Enter your first name.',
                    ],
                    'data' => $options['user'] ? $options['user']->getFirstname() : ''
                ])
                ->add('lastname', TextType::class, [
                    'purify_html' => true, 'constraints' => [
                        new NotBlank(),
                    ],
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Enter Last Name',
                        'title' => 'Enter your last name.',
                    ],
                    'data' => $options['user'] ? $options['user']->getLastname() : ''
                ])
                ->add('username', TextType::class, [
                    'purify_html' => true,
                    'required' => true, 'constraints' => [
                        new NotBlank(),
                    ],
                    'attr' => [
                        'placeholder' => 'Choose a Username',
                        'title' => 'Choose a unique username.',
                    ],
                    'data' => $options['user'] ? $options['user']->getUsername() : ''
                ])
                ->add('email', EmailType::class, [
                    'purify_html' => true,
                    'required' => true, 'constraints' => [
                        new NotBlank(),
                        new AcgEmailDomain([
                            'message' => 'Invalid email domain. Please use an @acg.edu email address.',
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => 'Enter Email',
                        'title' => 'Enter your email address.',
                    ],
                    'data' => $options['user'] ? $options['user']->getEmail() : ''
                ])
                ->add('isbusinessweek', ChoiceType::class, [
                    'label' => 'Are you a multi-ticket user?',
                    'required' => true, 'constraints' => [
                        new NotNull(),
                    ],
                    'choices' => [
                        'Yes' => true,
                        'No' => false,
                    ],
                    'expanded' => true, // Display as radio buttons
                    'data' => $options['user'] ? $options['user']->isIsbusinessweek() : false,
                    'help' => 'Select "Yes" if you are a multi-ticket user.',
                ])
                ->add('organizer', OrganizerRegistrationType::class, [
                    'help' => 'Complete organizer details if you are an organizer.'
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
        
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => User::class, // Set the entity class for your user
                'user' => null, // Pass the user object when creating the form
            ]);
        }
    }
