<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Event;
use App\Service\AppServices;
use App\Service\VenueService;
use App\Validator\Constraints\TagLimitConstraint;
use FOS\RestBundle\Controller\Annotations\Options;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EventTypeMultiEdit extends AbstractType
{
    private $services;

    private $venueService;

    public function __construct(AppServices $services, VenueService $venueService)
    {
        $this->services = $services;
        $this->venueService = $venueService;
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
            ->add('files', CollectionType::class, [
                'label' => 'Files',
                'entry_type' => EventFileType::class,
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
            ->add('open', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Do you want this event to be viewable by anyone?',
                'choices' => ['No' => false, 'Yes' => true],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'If yes, anyone from the internet can view this event',
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
            ->add('allowoverbook', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => '?',
                'choices' => ['No' => false, 'Yes' => true],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Allow volunteers to contact you if they want to help you prepare for the event',
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
    }

    private function getYears($min): array
    {
        $years = range(date('Y', strtotime('+2 years')), $min);
        return array_combine($years, $years);
    }

}
