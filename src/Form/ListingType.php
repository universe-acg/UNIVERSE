<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Listing;
use App\Entity\ListingCategory;
use App\Service\AppServices;
use App\Service\ListingCategoryService;
use App\Service\ListingService;
use App\Service\UserService;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ListingType extends AbstractType
{

    private $services;
    private $listingService;

    private $userService;

    private $listingCategoryService;

    public function __construct(AppServices $services, ListingService $listingService, UserService $userService, ListingCategoryService $listingCategoryService)
    {
        $this->services = $services;
        $this->listingService = $listingService;
        $this->userService = $userService;
        $this->listingCategoryService = $listingCategoryService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'purify_html' => true,
                'label' => 'Title',
                'required' => true,
                'help' => "Editing the title after the listing is saved won't change the listing URL.",
                'attr' => ['placeholder' => 'Enter the listing title'],
            ])
            ->add('shortdescription', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'shortdescclass',
                    'rows' => 4, // Set the number of rows to 3
                ],
                'label' => 'Short Description',
                'help' => 'Provide a detailed short description of the listing.',
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => ['class' => 'wysiwyg', 'id' => 'descriptionsummernote'],
                'label' => 'Full Description',
                'help' => 'Provide a detailed description of the listing.',
            ])
            ->add('category', EntityType::class, [
                'required' => true,
                'constraints' => array(new NotBlank()),
                'class' => ListingCategory::class,
                'choice_label' => 'name',
                'label' => 'Category',
                'help' => 'Select the appropriate category to help users understand the type of your listing.',
                'placeholder' => 'Select a Category',
                'query_builder' => function () {
                    return $this->listingCategoryService->getAvailableCategories();
                },
                'invalid_message' => 'Please select a category.',
            ])
            ->add('phonenumber', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Contact phone number',
                'help' => 'Enter the phone number to be contacted for listing inquiries.',
                'attr' => ['placeholder' => 'Enter contact phone number'],
            ])
            ->add('email', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Contact email address',
                'help' => 'Enter the email address to be reached for listing inquiries.',
                'attr' => ['placeholder' => 'Enter contact email address'],
            ])
//            ->add('tags', TextType::class, [
//                'purify_html' => true,
//                'required' => false,
//                'label' => 'Tags',
//                'help' => 'Enter keywords separated by commas to help attendees find your listing quickly (press Enter after each entry).',
//                'attr' => ['class' => 'tags-input', 'placeholder' => 'Enter listing tags'],
//                'constraints' => [
//                    new TagLimitConstraint(),
//                ],
//            ])
            ->add('imageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'label' => 'Main listing image',
                'help' => 'Choose the main image that represents your listing (recommended size: 1200x600px, 2:1 ratio).',
            ])
//            ->add('images', CollectionType::class, array(
//                'label' => 'Images gallery',
//                'entry_type' => ListingImageType::class,
//                'allow_add' => true,
//                'allow_delete' => true,
//                'prototype' => true,
//                'required' => false,
//                'by_reference' => false,
//                'attr' => array(
//                    'class' => 'form-collection',
//                ),
//                'help' => 'Add other images that represent your listing to be displayed as a gallery.',
//                'error_bubbling' => false,
//            ))
//            ->add('files', CollectionType::class, [
//                'label' => 'Files',
//                'entry_type' => ListingFileType::class, // Replace with your form type for handling files
//                'allow_add' => true,
//                'allow_delete' => true,
//                'prototype' => true,
//                'required' => false,
//                'by_reference' => false,
//                'attr' => [
//                    'class' => 'form-collection',
//                ],
//                'help' => 'Add files related to your listing.',
//                'error_bubbling' => false,
//            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Listing::class,
            'validation_groups' => ['create', 'update'],
        ]);

        $resolver->setDefined('user_choices'); // Define a custom option for user choices

        $resolver->setDefault('choices', function (Options $options) {
            // Return the choices as an array
            return $options['user_choices'];
        });
    }
}
