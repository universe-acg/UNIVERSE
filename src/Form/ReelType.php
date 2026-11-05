<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Reel;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ReelType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'purify_html' => true,
                'label' => 'Title',
                'attr' => [
                    'placeholder' => 'Enter the name of the reel',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Caption',
                'attr' => [
                    'rows' => 2,
                    'placeholder' => 'Provide the caption of the reel',
                ],
            ])
            ->add('mainImageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'label' => 'Thumbnail',
                'help' => 'Choose the thumbnail image',
                'data' => $options['data']->getMainImageFile(), // Pre-fill with the existing main image file name
            ])
            ->add('videoFile', VichFileType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => false,
                'label' => 'Video',
                'help' => 'Choose the video file',
                'data' => $options['data']->getVideoFile(), // Pre-fill with the existing video file name, if any
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Reel',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reel::class,
            'validation_groups' => ['create', 'update'],
        ]);

        $resolver->setDefined('user_choices'); // Define a custom option for user choices

        $resolver->setDefault('choices', function (Options $options) {
            // Return the choices as an array
            return $options['user_choices'];
        });
    }
}
