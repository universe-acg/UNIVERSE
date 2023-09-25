<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Audience;
use App\Entity\Event;
use App\Service\AppServices;
use FOS\RestBundle\Controller\Annotations\Options;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ->add('userEvents', ChoiceType::class, [
                'label' => 'Select an event to prefill your inputs',
                'choices' => $options['user_choices'],
                'required' => false, // Make it optional
                'mapped' => false, // This field won't be mapped to your entity
                'attr' => [
                    'id' => 'form_userEvents',
                ],
            ])
            ->add('name', TextType::class, [
                'purify_html' => true,
                'help' => "Editing the title after the event is saved won't change the event URL",
                'label' => 'Title',
                'required' => true,
//                    'data' => $responseData['name'] ?? '', // Set the initial value based on the received data or leave it blank
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => ['class' => 'wysiwyg', 'id' => 'descriptionsummernote'],
                'label' => 'Description',
//                    'data' => $responseData['description'] ?? '', // Set the initial value based on the received data or leave it blank
            ])
            ->add('showattendees', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Attendees',
                'choices' => ['Hide' => false, 'Show' => true],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Show the attendees number and list in the event page',
//                    'data' => $responseData['showattendees'] ?? false, // Set the initial value based on the received data or leave it as 'false'
            ])
            ->add('volsneeded', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Are you looking for volunteers?',
                'choices' => ['No' => false, 'Yes' => true],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Make it available for volunteers to contact you so they can help you prepare the event',
//                    'data' => $responseData['volsneeded'] ?? false, // Set the initial value based on the received data or leave it as 'false'
            ])
//            ->add('enablereviews', ChoiceType::class, [
//                'required' => true,
//                'multiple' => false,
//                'expanded' => true,
//                'label' => 'Enable reviews',
//                'choices' => ['Enable' => true, 'Disable' => false],
//                'label_attr' => ['class' => 'radio-custom radio-inline']
//            ])
//                ->add('country', EntityType::class, [
//                    'required' => false,
//                    'class' => Country::class,
//                    'choice_label' => 'name',
//                    'label' => 'Campus',
//                    'help' => "Select the campus in which your event takes place",
//                    'query_builder' => function () {
//                        return $this->services->getCountries(array());
//                    },
//                    'attr' => ['class' => 'select2', 'data-sort-options' => '1']
//                ])
//                ->add('youtubeurl', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'Youtube video url',
//                    'help' => 'If you have an Youtube video that represents your activities as an event organizer, add it in the standard format: https://www.youtube.com/watch?v=FzG4uDgje3M'
//                ])
//                ->add('externallink', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'External link',
//                    'help' => 'If your event has a dedicated website, enter its url here'
//                ])
            ->add('audiences', EntityType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'class' => Audience::class,
                'choice_label' => 'name',
                'label' => 'Audiences',
                'help' => 'Select the audience types that are targeted in your event',
                'label_attr' => ['class' => 'checkbox-custom checkbox-inline'],
//                    'query_builder' => function () {
//                        return $this->services->getAudiences(array());
//                    },
                'data' => isset($responseData['audiences']) ? $responseData['audiences']->toArray() : [], // Set the initial value based on the received data or an empty array
            ])
            ->add('phonenumber', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Contact phone number',
                'help' => 'Enter the phone number to be called for inquiries',
//                    'data' => $responseData['phonenumber'] ?? '', // Set the initial value based on the received data or leave it blank
            ])
            ->add('email', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Contact email address',
                'help' => 'Enter the email address to be reached for inquiries',
//                    'data' => $responseData['email'] ?? '', // Set the initial value based on the received data or leave it blank
            ])
//                ->add('twitter', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'Twitter',
//                ])
//                ->add('instagram', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'Instagram',
//                ])
//                ->add('facebook', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'Facebook',
//                ])
//                ->add('linkedin', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'LinkedIn',
//                ])
            ->add('tags', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Tags',
                'help' => 'To help attendee find your event quickly, enter some keywords that identify your event (press Enter after each entry)',
                'attr' => ['class' => 'tags-input'],
//                    'data' => $responseData['tags'] ?? '', // Set the initial value based on the received data or leave it blank
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
                'translation_domain' => 'messages'
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
//            ->add('repeat_days', NumberType::class, [
//                'label' => false, // Set the label to an empty string to hide it
//                'required' => false,
//                'attr' => [
//                    'id' => 'repeat-days',
//                    'pattern' => '^(0?[1-9]|[12][0-9]|3[0-1])$', // Regular expression for numbers from 1 to 31
//                    'title' => 'Please enter a number from 1 to 31', // Hint for the user
//                    'placeholder' => 'Enter number of days, for how often to repeat', // Add the placeholder text here
//                ],
//            ])
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
