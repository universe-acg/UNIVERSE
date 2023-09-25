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
                'label' => 'Organizer name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'About the organizer',
                'attr' => ['class' => 'wysiwyg']
            ])
//            ->add('categories', EntityType::class, [
//                'required' => false,
//                'multiple' => false, // Set this to false to allow selecting only one category
//                'expanded' => false,
//                'class' => Category::class,
//                'choice_label' => 'name',
//                'label' => 'Category', // You can use a singular label since only one category is allowed
//                'help' => 'Select the category that represents your account type',
//                'attr' => ['class' => 'select2'],
//                'query_builder' => function () {
//                    return $this->services->getCategories(array());
//                }
//            ])
//            ->add('categories', EntityType::class, [
//                'required' => false,
//                'multiple' => false,
//                'expanded' => false,
//                'class' => Category::class,
//                'choice_label' => 'name',
//                'label' => 'Category',
//                'help' => 'Select the category that represents your account type',
//                'attr' => ['class' => 'select2'],
//                'choices' => [
//                    'Club' => $this->services->getCategoryByName('Club'),
//                    'Office' => $this->services->getCategoryByName('Office'),
//                    'Society' => $this->services->getCategoryByName('Society'),
//                ],
//            ])
            ->add('logoFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => 'Organizer logo',
                'help' => 'Optionally add a cover photo to showcase your organizer activities. Only PNG, JPEG with max size 5M are allowed.',
                'constraints' => [
                    new File([
                        'maxSize' => '5M', // Set the maximum upload size (e.g., 5 megabytes)
                        'mimeTypes' => [
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
                'help' => 'Optionally add a cover photo to showcase your organizer activities. Only PNG, JPEG with max size 5M are allowed.',
                'constraints' => [
                    new File([
                        'maxSize' => '5M', // Set the maximum upload size (e.g., 5 megabytes)
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                    ]),
                ],
            ])
//                ->add('country', EntityType::class, [
//                    'required' => false,
//                    'class' => Country::class,
//                    'choice_label' => 'name',
//                    'label' => 'Country',
//                    'attr' => ['class' => 'select2'],
//                    'query_builder' => function () {
//                        return $this->services->getCountries(array());
//                    }
//                ])
//            ->add('website', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Website'
//            ])
            ->add('email', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Email'
            ])
//            ->add('phone', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Phone'
//            ])
//            ->add('facebook', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Facebook'
//            ])
//            ->add('twitter', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Twitter'
//            ])
            ->add('instagram', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Instagram'
            ])
//                ->add('googleplus', TextType::class, [
//                    'purify_html' => true,
//                    'required' => false,
//                    'label' => 'Google Plus'
//                ])
//            ->add('linkedin', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'LinkedIn'
//            ])
//            ->add('youtubeurl', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Youtube video url',
//                'help' => 'If you have an Youtube video that represents your activities as an event organizer, add it in the standard format: https://www.youtube.com/watch?v=FzG4uDgje3M'
//            ])
//                ->add('showvenuesmap', ChoiceType::class, [
//                    'required' => true,
//                    'multiple' => false,
//                    'expanded' => true,
//                    'label' => 'Show venues map',
//                    'choices' => ['Show' => true, 'Hide' => false],
//                    'label_attr' => ['class' => 'radio-custom radio-inline'],
//                    'help' => 'Show a map at the bottom of your organizer profile page containing the venues you added'
//                ])
            ->add('showfollowers', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Show followers',
                'choices' => ['Show' => true, 'Hide' => false],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Show the number and list of people that follow you'
            ])
//                ->add('showreviews', ChoiceType::class, [
//                    'required' => true,
//                    'multiple' => false,
//                    'expanded' => true,
//                    'label' => 'Show reviews',
//                    'choices' => ['Show' => true, 'Hide' => false],
//                    'label_attr' => ['class' => 'radio-custom radio-inline'],
//                    'help' => 'Show the reviews that you received for your events'
//                ])
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
