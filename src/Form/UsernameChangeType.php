<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Form;

    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\Callback;
    use Symfony\Component\Validator\Context\ExecutionContextInterface;

    class UsernameChangeType extends AbstractType
    {
        private $entityManager;

        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }

        public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder
                ->add('username', TextType::class, [
                    'label' => 'New Username',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Enter your new username',
                        'help' => 'Choose a unique username for your account.',
                    ],
                    'constraints' => [
                        new Callback([$this, 'validateUniqueUsername']),
                    ],
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Save Username',
                    'attr' => ['class' => 'btn btn-primary'],
                ]);
        }

        public function configureOptions(OptionsResolver $resolver): void
    {
            $resolver->setDefaults([
                'data_class' => User::class,
            ]);
        }

        /**
         * Custom callback function to validate the uniqueness of the username.
         */
        public function validateUniqueUsername($value, ExecutionContextInterface $context)
        {
            // Get the current user
            $currentUser = $context->getRoot()->getData();

            // Get the existing user with the same username
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $value]);

            // Check if there is an existing user with a different username
            if ($existingUser && $existingUser !== $currentUser) {
                // Add a violation if the username is not unique
                $context->buildViolation('This username is already in use.')
                    ->atPath('username')
                    ->addViolation();
            }
        }
    }
