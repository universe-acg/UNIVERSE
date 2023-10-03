<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Form;
    
    use App\Entity\Event;
    use App\Service\AppServices;
    use FOS\RestBundle\Controller\Annotations\Options;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\CollectionType;
    use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Vich\UploaderBundle\Form\Type\VichImageType;
    
    class EventTypeMultiEdit extends AbstractType
    {
        private $services;
        
        public function __construct(AppServices $services)
        {
            $this->services = $services;
        }
        
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('name', TextType::class, [
                    'purify_html' => true,
                    'help' => "Editing the title after the event is saved won't change the event URL",
                    'label' => 'Title',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'E.g., My Awesome Event',
                    ],
                ])
                ->add('description', TextareaType::class, [
                    'required' => true,
                    'attr' => ['class' => 'wysiwyg', 'id' => 'descriptionsummernote'],
                    'label' => 'Description',
                ])
                ->add('showattendees', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Attendees',
                    'choices' => ['Hide' => false, 'Show' => true],
                    'label_attr' => ['class' => 'radio-custom radio-inline'],
                    'help' => 'Show the number of attendees and attendee list on the event page',
                ])
                ->add('volsneeded', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Are you looking for volunteers?',
                    'choices' => ['No' => false, 'Yes' => true],
                    'label_attr' => ['class' => 'radio-custom radio-inline'],
                    'help' => 'Allow volunteers to contact you if they want to help you prepare for the event',
                ])
                ->add('phonenumber', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Contact Phone Number',
                    'help' => 'Enter the phone number for inquiries',
                    'attr' => [
                        'placeholder' => 'E.g., +1 (123) 456-7890',
                    ],
                ])
                ->add('email', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Contact Email Address',
                    'help' => 'Enter the email address for inquiries',
                    'attr' => [
                        'placeholder' => 'E.g., example@email.com',
                    ],
                ])
                ->add('tags', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Tags',
                    'help' => 'Enter keywords that identify your event (press Enter after each entry)',
                    'attr' => [
                        'class' => 'tags-input',
                        'placeholder' => 'E.g., Assembly, Conference, Workshop',
                    ],
                ])
                ->add('imageFile', VichImageType::class, [
                    'required' => true,
                    'allow_delete' => false,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'imagine_pattern' => 'scale',
                    'label' => 'Main Event Image',
                    'help' => 'Select an image to represent your event (Recommended size: 1200x600px)',
                ])
                ->add('images', CollectionType::class, [
                    'label' => 'Images Gallery',
                    'entry_type' => EventImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'required' => false,
                    'by_reference' => false,
                    'attr' => [
                        'class' => 'form-collection',
                    ],
                    'help' => 'Add other images that represent your event to create a gallery',
                ])
                ->add('open', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Do you want this event to be viewable by anyone?',
                    'choices' => ['No' => false, 'Yes' => true],
                    'label_attr' => ['class' => 'radio-custom radio-inline'],
                    'help' => 'If yes, anyone from the internet can view this event',
                ])
                ->add('eventdates', CollectionType::class, [
                    'label' => 'Event Dates',
                    'entry_type' => EventDateTypeEdit::class,
                    'allow_add' => false,
                    'allow_delete' => false,
                    'prototype' => true,
                    'prototype_name' => '__eventdate__',
                    'required' => true,
                    'by_reference' => false,
                    'attr' => [
                        'class' => 'form-collection eventdates-collection manual-init',
                        'disabled' => true,
                    ],
                ])
                ->add('repeatsweekly', ChoiceType::class, [
                    'required' => false,
                    'label' => 'Does this event repeat weekly/monthly?',
                    'choices' => [
                        'Yes' => 'yes',
                        'No' => 'no',
                    ],
                    'expanded' => true, // Display as radio buttons
                    'multiple' => false, // Allow only one selection
                    'attr' => [
                        'title' => 'Select whether the event repeats weekly or monthly.',
                        'class' => 'repeats-weekly-field',
                    ],
                ])
                ->add('repeatweeks', ChoiceType::class, [
                    'required' => false,
                    'label' => 'Repeat every week for how many weeks (after this current week ends)',
                    'choices' => [
                        'Month 1' => [
                            '1 Week' => 1,
                            '2 Weeks' => 2,
                            '3 Weeks' => 3,
                            '4 Weeks' => 4,
                        ],
                        'Month 2' => [
                            '5 Weeks' => 5,
                            '6 Weeks' => 6,
                            '7 Weeks' => 7,
                            '8 Weeks' => 8,
                        ],
                        'Month 3' => [
                            '9 Weeks' => 9,
                            '10 Weeks' => 10,
                            '11 Weeks' => 11,
                            '12 Weeks' => 12,
                        ],
                        'Month 4' => [
                            '13 Weeks' => 13,
                            '14 Weeks' => 14,
                            '15 Weeks' => 15,
                            '16 Weeks' => 16,
                        ],
                    ],
                    'choice_attr' => function ($choice, $key, $value) {
                        // Add attributes to group headings (months)
                        if (is_array($value)) {
                            return ['disabled' => 'disabled', 'class' => 'group-heading'];
                        }
                        // Add attributes to week options (e.g., '1 Week', '2 Weeks', etc.)
                        return [];
                    },
                    'expanded' => false, // Set to true if you want radio buttons
                    'multiple' => false, // Set to true if you want multiple selections
                    'attr' => [
                        'title' => 'Select the number of weeks for which the event should repeat.',
                        'class' => 'repetition-options',
                    ],
                ])
//                ->add('repeatstartdate', DateTimeType::class, [
//                    'required' => false,
//                    'label' => 'Repetition Start Date and Time',
//                    'widget' => 'single_text',
//                    'help' => 'Ideally select the date the event stars/takes place and X weeks later the first repetition will take place',
//                    'html5' => false,
//                    'attr' => [
//                        'class' => 'datepicker repetition-options',
//                        'autocomplete' => 'off',
//                        'placeholder' => 'Select the repetition start date and time (e.g., yyyy-MM-dd HH:mm)',
//                        'title' => 'Select the start date and time for the repetition of the event.',
//                    ],
//                    'format' => 'yyyy-MM-dd HH:mm',
//                ])
                ->add('repeatenddate', DateTimeType::class, [
                    'required' => false,
                    'label' => 'Repetition End Date ',
                    'widget' => 'single_text',
                    'html5' => false,
                    'help' => 'The event repetitions will stop at the end of this day, even if you have set it for more weeks',
                    'attr' => [
                        'class' => 'datepicker repetition-options',
                        'autocomplete' => 'off',
                        'placeholder' => 'Select the Repetition End Date  (e.g., yyyy-MM-dd HH:mm)',
                        'title' => 'Select the end date and time for the repetition of the event.',
                    ],
                    'format' => 'yyyy-MM-dd HH:mm',
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Save',
                    'attr' => ['class' => 'btn btn-primary'],
                ]);
        }
        
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => Event::class,
                'validation_groups' => ['create', 'update']
            ]);
            
            $resolver->setDefined('user_choices'); // Define a custom option for user choices
            
            $resolver->setDefault('choices', function (Options $options) {
                // Return the choices as an array
                return $options['user_choices'];
            });
        }
        
        private function getYears($min): array
        {
            $years = range(date('Y', strtotime('+2 years')), $min);
            return array_combine($years, $years);
        }
    }
