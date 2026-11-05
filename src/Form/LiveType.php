<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Live;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LiveType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'purify_html' => true,
                'label' => 'Livestream Title',
                'attr' => [
                    'placeholder' => 'Enter the name of the livestream',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Livestream Description',
                'attr' => [
                    'placeholder' => 'Provide a description of the livestream',
                    'class' => 'wysiwyg'
                ],
            ])
            ->add('mainImageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'label' => 'Main live image',
                'help' => 'Choose the main image of the livestream',
                'data' => $options['data']->getMainImageFile(), // Pre-fill with the existing main image file name
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Livestream',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Live::class,
            'validation_groups' => ['create', 'update'],
        ]);

        $resolver->setDefined('user_choices'); // Define a custom option for user choices

        $resolver->setDefault('choices', function (Options $options) {
            // Return the choices as an array
            return $options['user_choices'];
        });
    }
}
