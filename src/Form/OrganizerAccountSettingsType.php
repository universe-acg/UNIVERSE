<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Form;

    use App\Entity\User;
    use App\Service\AppServices;
    use App\Validator\Constraints\AcgEmailDomain;
    use App\Validator\Constraints\UniqueEmail;
    use App\Validator\Constraints\UniqueUsername;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Context\ExecutionContextInterface;

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

        public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder
                ->add('email', EmailType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Email',
                    'constraints' => [
                        new NotBlank(),
                        new AcgEmailDomain([
                            'message' => 'Invalid email domain. Please use an @acg.edu email address.',
                        ]),
//                    $options['isEmailUnique'] ? new UniqueEmail() : null,
                    ],
                    'disabled' => true,
                    'help' => 'In order to update your email address, please contact: universe@acg.edu.',
                    'attr' => ['placeholder' => 'Enter your email'],
                ])
                ->add('username', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Username',
                    'constraints' => [
                        new Length([
                            'min' => 2,
                            'max' => 30,
                            'minMessage' => 'Username must be at least {{ limit }} characters long.',
                            'maxMessage' => 'Username cannot be longer than {{ limit }} characters.',
                        ]),
                        $options['isUsernameUnique'] ? new UniqueUsername() : null,
                    ],
                    'disabled' => true,
                    'help' => 'In order to update your username, please contact: universe@acg.edu.',
                    'attr' => ['placeholder' => 'Enter your username'],
                ])
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
                ->add('save', SubmitType::class, [
                    'label' => 'Save',
                    'attr' => ['class' => 'btn btn-primary'],
                ]);
        }

        public function configureOptions(OptionsResolver $resolver): void
    {
            $resolver->setDefaults([
                'data_class' => User::class,
                'isEmailUnique' => true, // Set to true if email should be unique
                'isUsernameUnique' => true, // Set to true if username should be unique
            ]);
        }
    }
