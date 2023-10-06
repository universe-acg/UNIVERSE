<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Form;
    
    use App\Entity\EventTicket;
    use App\Service\AppServices;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
    use Symfony\Component\Form\Extension\Core\Type\HiddenType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\NotBlank;
    
    class EventTicketType extends AbstractType
    {
        
        private $services;
        
        public function __construct(AppServices $services)
        {
            $this->services = $services;
        }
        
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('name', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Ticket Name',
                    'help' => 'Enter a name for the ticket (e.g., Ticket, General Admission,)',
                    'attr' => [
                        'placeholder' => 'E.g., Ticket, General Admission',
                    ],
                ])
                ->add('quantity', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Total Available Tickets',
                    'attr' => [
                        'class' => 'touchspin-integer event-date-ticket-quantity',
                        'placeholder' => 'Enter the total number of tickets available',
                    ],
                    'help' => 'Specify the total number of tickets available for this ticket type.',
                    'constraints' => array(
                        new NotBlank()
                    ),
                ])
                ->add('salesstartdate', DateTimeType::class, [
                    'required' => true,
                    'label' => 'Ticket Registration Starts On',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => [
                        'class' => 'datetimepicker',
                        'autocomplete' => 'off',
                        'placeholder' => 'Select the start date and time',
                    ],
                    'format' => 'yyyy-MM-dd HH:mm',
                    'help' => 'Choose the date and time when registration for this ticket type starts.',
                    'constraints' => array(
                        new NotBlank()
                    ),
                ])
                ->add('salesenddate', DateTimeType::class, [
                    'required' => true,
                    'label' => 'Ticket Registration Ends On',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => [
                        'class' => 'datetimepicker',
                        'autocomplete' => 'off',
                        'placeholder' => 'Select the end date and time',
                    ],
                    'format' => 'yyyy-MM-dd HH:mm',
                    'help' => 'Choose the date and time when registration for this ticket type ends.',
                    'constraints' => array(
                        new NotBlank()
                    ),
                ])
                ->add('position', HiddenType::class, [
                    'attr' => [
                        'class' => 'event-date-ticket-position',
                    ],
                ]);
        }
        
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => EventTicket::class,
                'validation_groups' => ['create', 'update'],
            ]);
        }
    }
