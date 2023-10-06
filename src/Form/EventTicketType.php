<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Form;
    
    use App\Entity\EventTicket;
    use App\Service\AppServices;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\HiddenType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
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
                    'help' => 'Enter a name for the ticket (e.g., Ticket, General Admission), or leave blank if not needed',
                    'attr' => [
                        'placeholder' => 'E.g., Ticket, General Admission',
                    ],
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
