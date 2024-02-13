<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Organizer;
use App\Service\AppServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class OrganizerProfileType extends AbstractType
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
                'required' => true,
                'label' => 'Organizer name',
                'attr' => ['placeholder' => 'Enter the creator name'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'About the organizer',
                'attr' => [
                    'class' => 'wysiwyg',
                    'placeholder' => 'Tell us about your organizer...',
                ],
            ])
            ->add('logoFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => 'Organizer logo',
                'help' => 'Upload a logo to represent your organizer. Supported formats: PNG, JPEG (max size: 5MB).',
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                    ]),
                ],
            ])
            ->add('coverFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => 'Cover photo',
                'help' => 'Upload a cover photo to showcase your organizer activities. Supported formats: PNG, JPEG (max size: 5MB).',
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Email',
                'attr' => ['placeholder' => 'Enter the creator email'],
            ])
            ->add('instagram', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Instagram',
                'attr' => ['placeholder' => 'Enter the Instagram link'],
            ])
            ->add('showfollowers', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Show followers',
                'choices' => ['Show' => true, 'Hide' => false],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Choose whether to display the number and list of people that follow your organizer.',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary btn-block'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organizer::class,
        ]);
    }
}
