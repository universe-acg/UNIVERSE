<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Form;
    
    use App\Entity\User;
    use App\Service\AppServices;
    use App\Validator\Constraints\UniqueEmail;
    use App\Validator\Constraints\UniqueUsername;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\Email as SymfonyEmail;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Context\ExecutionContextInterface;

// Add this use statement
    
    class OrganizerAccountSettingsType extends AbstractType
    {
        private $services;
        
        public function __construct(AppServices $services)
        {
            $this->services = $services;
        }
        
        /**
         * @param $value
         * @param ExecutionContextInterface $context
         * @return void
         */
        public function validateUniqueEmail($value, ExecutionContextInterface $context)
        {
            if (!$value) {
                return;
            }
            
            // Get the current user
            $currentUser = $this->getUser();
            
            // Get the existing user with the same email
            $existingUser = $this->services->getUserByEmail($value);
            
            // Check if there is an existing user with a different email
            if ($existingUser && $existingUser !== $currentUser && $existingUser->getEmail() !== $value) {
                $context->buildViolation(new UniqueEmail())->addViolation();
            }
        }
        
        /**
         * @param $value
         * @param ExecutionContextInterface $context
         * @return void
         */
        public function validateUniqueUsername($value, ExecutionContextInterface $context)
        {
            if (!$value) {
                return;
            }
            
            // Get the current user
            $currentUser = $this->getUser();
            
            // Check if the submitted username is the same as the current user's username
            if ($currentUser && $currentUser->getUsername() === $value) {
                return;
            }
            
            // Get the existing user with the same username
            $existingUser = $this->services->getUserByUsername($value);
            
            // Check if there is an existing user with a different username
            if ($existingUser) {
                $context->buildViolation(new UniqueUsername())->addViolation();
            }
        }
        
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('email', EmailType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Email',
                    'constraints' => array(
                        new SymfonyEmail(),
                        // new NotBlank(),
//                        new UniqueEmail(),
                        new Length([
                            'min' => 2,
                            'max' => 30])
                    ),
                    'disabled' => true,
                    'help' => 'In order to update your email address please contact: universe@acg.edu.',
                ])
                ->add('username', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Username',
                    'constraints' => array(
                        // new NotBlank(),
//                        new UniqueUsername(),
                        new Length([
                            'min' => 2,
                            'max' => 30])
                    ),
                    'disabled' => true,
                    'help' => 'In order to update your username please contact: universe@acg.edu.',
                ])
                ->add('firstname', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'First name'
                ])
                ->add('lastname', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Last name'
                ])
//            ->add('avatarFile', VichImageType::class, [
//                'required' => false,
//                'allow_delete' => true,
//                'download_label' => false,
//                'download_uri' => false,
//                'image_uri' => false,
//                'imagine_pattern' => 'scale',
//                'label' => 'Profile picture',
//                'translation_domain' => 'messages'
//            ])
//                ->add('street', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'Street address',
//                ])
//                ->add('street2', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'disabled' => true,
//                    'label' => 'Street address 2',
//                ])
//                ->add('city', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'City',
//                ])
//                ->add('postalcode', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'Zip / Postal code',
//                ])
//                ->add('state', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'disabled' => true,
//                    'label' => 'State',
//                ])
//                ->add('country', EntityType::class, [
//                    'required' => false,
//                    'class' => Country::class,
//                    'choice_label' => 'name',
//                    'label' => 'Country',
//                    'attr' => ['class' => 'select2'],
//                    'placeholder' => 'Select an option',
//                    'query_builder' => function () {
//                        return $this->services->getCountries(array());
//                    },
//                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Save',
                    'attr' => ['class' => 'btn btn-primary'],
                ]);
        }
        
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => User::class,
            ]);
        }
        
    }
