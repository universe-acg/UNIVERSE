<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Event;
use App\Service\AppServices;
use App\Service\UserService;
use App\Service\VenueService;
use App\Validator\Constraints\TagLimitConstraint;

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

class EventTypeMulti extends AbstractType
{

    private $services;
    private $venueService;

    private $userService;

    public function __construct(AppServices $services, VenueService $venueService, UserService $userService)
    {
        $this->services = $services;
        $this->venueService = $venueService;
        $this->userService = $userService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userEvents', ChoiceType::class, [
                'label' => 'Select an event to prefill your inputs',
                'choices' => $options['user_choices'],
                'required' => false,
                'help' => "Select an event from the list to automatically fill in the form with its details.",
                'mapped' => false,
                'attr' => [
                    'id' => 'form_userEvents',
                ],
            ])
            ->add('name', TextType::class, [
                'purify_html' => true,
                'label' => 'Title',
                'required' => true,
                'help' => "Editing the title after the event is saved won't change the event URL.",
                'attr' => ['placeholder' => 'Enter the event title'],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => ['class' => 'wysiwyg', 'id' => 'descriptionsummernote'],
                'label' => 'Description',
                'help' => 'Provide a detailed description of the event.',
            ])
            ->add('showattendees', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Attendees',
                'choices' => ['Hide' => false, 'Show' => true],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Choose whether to display the number of attendees and the attendee list on the event page.',
            ])
            ->add('volsneeded', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Are you looking for volunteers?',
                'choices' => ['No' => false, 'Yes' => true],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Indicate whether you need volunteers for your event preparation.',
            ])
            ->add('phonenumber', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Contact phone number',
                'help' => 'Enter the phone number to be contacted for event inquiries.',
                'attr' => ['placeholder' => 'Enter contact phone number'],
            ])
            ->add('email', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Contact email address',
                'help' => 'Enter the email address to be reached for event inquiries.',
                'attr' => ['placeholder' => 'Enter contact email address'],
            ])
            ->add('tags', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Tags',
                'help' => 'Enter keywords separated by commas to help attendees find your event quickly (press Enter after each entry).',
                'attr' => ['class' => 'tags-input', 'placeholder' => 'Enter event tags'],
                'constraints' => [
                    new TagLimitConstraint(),
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'label' => 'Main event image',
                'help' => 'Choose the main image that represents your event (recommended size: 1200x600px, 2:1 ratio).',
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
                'help' => 'Add other images that represent your event to be displayed as a gallery.',
                'error_bubbling' => false,
            ))
            ->add('files', CollectionType::class, [
                'label' => 'Files',
                'entry_type' => EventFileType::class, // Replace with your form type for handling files
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-collection',
                ],
                'help' => 'Add files related to your event.',
                'error_bubbling' => false,
            ])
            ->add('outofcampus', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Is this in an out of campus event?',
                'choices' => ['No' => false, 'Yes' => true],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
            ])
            ->add('open', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'If yes, anyone from the internet can view this event',
                'choices' => ['No' => false, 'Yes' => true],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Do you want this event to be viewable by anyone (unauthenticated users, people from all over the world who do not have an account at Universe)?',
            ])
            ->add('outofcampus', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Is this in an out of campus event?',
                'choices' => ['No' => false, 'Yes' => true],
                'data' => false,  // Set the default value to false
                'label_attr' => ['class' => 'radio-custom radio-inline'],
            ])
            ->add('venue', ChoiceType::class, [ // Venue field added here
                'required' => true,
                'label' => 'Select Venue',
                'placeholder' => 'Choose a venue',
                'choices' => $this->getVenuesChoices(),
                'attr' => [
                    'class' => 'venue-field', // Add a CSS class
                ],
            ])
            ->add('eventdates', CollectionType::class, [
                'label' => 'Event dates',
                'entry_type' => EventDateTypeMulti::class,
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
                'help' => 'Add the dates and times for your event.',
            ])
            ->add('repeatsweekly', ChoiceType::class, [
                'required' => true,
                'label' => 'Does this event repeat weekly/monthly?',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'data' => false,
                'expanded' => true, // Display as radio buttons
                'multiple' => false, // Allow only one selection
                'label_attr' => [
                    'title' => 'Select whether the event repeats weekly or monthly.',
                    'class' => 'repeats-weekly-field radio-custom radio-inline',
                ],
            ])
            ->add('repeatweeks', ChoiceType::class, [
                'required' => false,
                'label' => 'Repeat every week for how many weeks (after this current week ends)',
                'choices' => [
                    'Every 1 Week' => 1,
                    'Every 2 Weeks' => 2,
                    'Every 3 Weeks' => 3,
                    'Every 4 Weeks' => 4,
                    'Every 5 Weeks' => 5,
                    'Every 6 Weeks' => 6,
                    'Every 7 Weeks' => 7,
                    'Every 8 Weeks' => 8,
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
            // Add this code to your EventTypeMulti class
            ->add('publishOnSpecificDate', ChoiceType::class, [
                'label' => 'Publish on a specific date?',
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'publish-on-specific-date-field radio-custom radio-inline',
                ],
                'data' => false, // Preselect "No"
            ])
            ->add('publishDate', DateTimeType::class, [
                'required' => false,
                'label' => 'Select the publication date and time',
                'widget' => 'single_text', // Use single_text widget
                'html5' => false,          // Disable HTML5 rendering
                'attr' => [
                    'class' => 'datepicker publish-date-picker',
                    'autocomplete' => 'off',
                    'style' => 'display: none;', // Hide initially
                    'placeholder' => 'Select the publication date and time (e.g., yyyy-MM-dd HH:mm)',
                ],
                'format' => 'yyyy-MM-dd HH:mm', // Specify the format to include date and time
            ])
            ->add('viewablebyselect', ChoiceType::class, [
                'label' => 'Is this a private event to be viewable only by a select few?',
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => ' radio-custom radio-inline',
                ],
                'data' => false, // Preselect "No"
            ])
            ->add('visibletoallmembers', ChoiceType::class, [
                'label' => 'Make it viewable only to all members?',
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
                'required' => true,
                'help' => "Quickly add all your members in the list of specific users",
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'radio-custom radio-inline',
                ],
                'data' => false, // Preselect "No"
            ])
            ->add('selectattendees', ChoiceType::class, [ // Venue field added here
                'required' => true,
                'multiple' => true, // Allow multiple selections
                'label' => 'Select specific users',
                'placeholder' => 'Choose users',
                'help' => "If they are duplicates here, do not worry, they will be removed",
                'choices' => $this->getAllAttendees(),
                'attr' => [
                    'class' => 'select2', // Add a CSS class
                ],
            ])
            ->add('redirectable', ChoiceType::class, [
                'label' => 'Is this an event that redirects somewhere?',
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => ' radio-custom radio-inline',
                ],
                'data' => false, // Preselect "No"
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    private function getVenuesChoices()
    {
        $venues = $this->venueService->getVenues(array('hidden' => false));
        $venueChoices = [];
        foreach ($venues as $venue) {
            $venueChoices[$venue->getTitle()] = $venue;
        }
        return $venueChoices;
    }

    private function getAllAttendees()
    {
        $users = $this->userService->getAttendees();
        $userChoices = [];
        foreach ($users as $user) {
            $userChoices[$user->getCrossRoleName() . " | " . $user->getEmail()] = $user;
        }
        return $userChoices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'validation_groups' => ['create', 'update'],
        ]);

        $resolver->setDefined('user_choices'); // Define a custom option for user choices

        $resolver->setDefault('choices', function (Options $options) {
            // Return the choices as an array
            return $options['user_choices'];
        });

//        $this->getAllMembers();
    }

    private function getAllMembers()
    {
        $users = $this->userService->getMembers();
        $userChoices = [];
        foreach ($users as $user) {
            $userChoices[$user->getCrossRoleName() . " | " . $user->getEmail()] = $user;
        }
        return $userChoices;
    }

    private function getYears($min): array
    {
        $years = range(date('Y', strtotime('+2 years')), $min);
        return array_combine($years, $years);
    }
}
