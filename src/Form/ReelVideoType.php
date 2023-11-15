<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Reel;
use FOS\RestBundle\Controller\Annotations\Options;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ReelVideoType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'purify_html' => true,
                'label' => 'Title',
                'attr' => [
                    'placeholder' => 'Enter the name of the reel',
                ],
            ])
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => 'Caption',
                'attr' => [
                    'rows' => 2,
                    'placeholder' => 'Provide the caption of the reel',
                ],
            ])
            ->add('videoFile', VichFileType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please choose a file.',
                    ]),
                    new File([
                        'maxSize' => '25M',
                        'mimeTypes' => ['video/mp4'],
                        'mimeTypesMessage' => 'Please upload a valid MP4 video file.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Choose a Video',
                    'class' => 'inputvideo',
                ],
                'help' => 'Choose the video file',
                'data' => $options['data']->getVideoFile(), // Pre-fill with the existing main image file name
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Reel',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
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
