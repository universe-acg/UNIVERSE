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
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\NotNull;
    use Vich\UploaderBundle\Form\Type\VichImageType;
    
    class EventType extends AbstractType
    {
        
        private $services;
        
        public function __construct(AppServices $services)
        {
            $this->services = $services;
        }
        
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
//                ->add('userEvents', ChoiceType::class, [
//                    'label' => 'Select an event to prefill your inputs',
//                    'choices' => $options['user_choices'],
//                    'required' => false, // Make it optional
//                    'mapped' => false, // This field won't be mapped to your entity
//                    'attr' => [
//                        'id' => 'form_userEvents',
//                    ],
//                ])
                ->add('name', TextType::class, [
                    'purify_html' => true,
                    'help' => "Editing the title after the event is saved won't change the event URL",
                    'label' => 'Title',
                    'required' => true,
                    'constraints' => array(
                        new NotBlank()
                    ),
                ])
                ->add('description', TextareaType::class, [
                    'required' => true,
                    'attr' => ['class' => 'wysiwyg', 'id' => 'descriptionsummernote'],
                    'label' => 'Description',
                    'constraints' => array(
                        new NotBlank()
                    ),
                ])
                ->add('showattendees', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Attendees',
                    'choices' => ['Hide' => false, 'Show' => true],
                    'label_attr' => ['class' => 'radio-custom radio-inline'],
                    'help' => 'Show the attendees number and list in the event page',
                    'constraints' => array(
                        new NotNull()
                    ),
                ])
                ->add('volsneeded', ChoiceType::class, [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Are you looking for volunteers?',
                    'choices' => ['No' => false, 'Yes' => true],
                    'label_attr' => ['class' => 'radio-custom radio-inline'],
                    'help' => 'Make it available for volunteers to contact you so they can help you prepare the event',
                    'constraints' => array(
                        new NotNull()
                    ),
                ])
                ->add('phonenumber', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Contact phone number',
                    'help' => 'Enter the phone number to be called for inquiries',
                ])
                ->add('email', TextType::class, [
                    'purify_html' => true,
                    'required' => true,
                    'label' => 'Contact email address',
                    'help' => 'Enter the email address to be reached for inquiries',
                    'constraints' => array(
                        new NotBlank()
                    ),
                ])
                ->add('tags', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Tags',
                    'help' => 'To help attendee find your event quickly, enter some keywords that identify your event (press Enter after each entry)',
                    'attr' => ['class' => 'tags-input'],
                ])
                ->add('imageFile', VichImageType::class, [
                    'required' => true,
                    'allow_delete' => false,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'imagine_pattern' => 'scale',
                    'label' => 'Main event image',
                    'help' => 'Choose the right image to represent your event (We recommend using at least a 1200x600px (2:1 ratio) image )',
                    'constraints' => array(
                        new NotBlank()
                    ),
                ])
                ->add('images', CollectionType::class, array(
                    'label' => 'Images gallery',
                    'entry_type' => EventImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'required' => false,
                    'by_reference' => false,
                    'attr' => array(
                        'class' => 'form-collection',
                    ),
                    'help' => 'Add other images that represent your event to be displayed as a gallery',
                    'error_bubbling' => false,
                ))
//            ->add('isrepeat', ChoiceType::class, [
//                'required' => true,
//                'multiple' => false,
//                'expanded' => true,
//                'label' => 'Will this event repeat like weekly, monthly, etc.?',
//                'choices' => ['No' => false, 'Yes' => true],
//                'label_attr' => ['class' => 'radio-custom radio-inline'],
//                'help' => 'If this event will repeat weekly or monthly, or during some other time frame, please mark it as so',
//                'attr' => [
//                    // Add an id to the "Yes" option
//                    'id' => 'repeat-yes',
//                ],
//            ])
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
                    'label' => 'Event dates',
                    'entry_type' => EventDateType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'prototype_name' => '__eventdate__',
                    'required' => true,
                    'by_reference' => false,
                    'error_bubbling' => false,
                    'attr' => [
                        'class' => 'form-collection eventdates-collection manual-init',
                    ],
                ])
                ->add('repeatsweekly', ChoiceType::class, [
                    'required' => true,
                    'data' => false,
                    'label' => 'Does this event repeat weekly/monthly?',
                    'choices' => [
                        'Yes' => true,
                        'No' => false,
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
                    'format' => 'yyyy-MM-dd',
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
                return $options['user_choices'];
            });
        }
    }
