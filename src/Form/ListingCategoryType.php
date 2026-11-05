<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\ListingCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingCategoryType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'Category Name',
                'purify_html' => true,
                'attr' => [
                    'placeholder' => 'Enter a Category Name (e.g., Work-study, Volunteer, etc.)',
                ],
            ])
//            ->add('icon', TextType::class, [
//                'required' => true,
//                'label' => 'Icon',
//                'purify_html' => true,
//                'attr' => [
//                    'class' => 'icon-picker',
//                    'autocomplete' => 'disabled',
//                    'placeholder' => 'Enter an Icon (e.g., fa fa-plane)',
//                ],
//            ])
//            ->add('imageFile', VichImageType::class, [
//                'required' => false,
//                'allow_delete' => true,
//                'download_label' => false,
//                'download_uri' => false,
//                'image_uri' => false,
//                'imagine_pattern' => 'scale',
//                'label' => 'Image',
//                'attr' => [
//                    'placeholder' => 'Choose an Image',
//                ],
//            ])
//            ->add('featuredorder', ChoiceType::class, [
//                'required' => false,
//                'label' => 'Featured Order',
//                'choices' => [
//                    '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5,
//                    '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10,
//                    '11' => 11, '12' => 12, '13' => 13, '14' => 14, '15' => 15
//                ],
//                'help' => 'Set the display order for the featured categories',
//                'placeholder' => 'Select Featured Order',
//            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ListingCategory::class,
        ]);
    }

}
