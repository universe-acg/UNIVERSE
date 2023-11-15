<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Event;
use App\Service\AppServices;
use App\Service\UserService;
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

class EventTypeMultiSimple extends AbstractType
{

    private $services;
    private $userService;

    public function __construct(AppServices $services, UserService $userService)
    {
        $this->services = $services;
        $this->userService = $userService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
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
}
