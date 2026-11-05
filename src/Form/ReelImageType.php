<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Reel;
use App\Service\ReelService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ReelImageType extends AbstractType
{
    private $reelService;

    public function __construct(ReelService $reelService)
    {
        $this->reelService = $reelService;
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
                'download_uri' => true,
                'image_uri' => true,
                'label' => 'Thumbnail',
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                    ]),
                ],
                'attr' => ['placeholder' => 'Choose an Image', 'class' => 'inputimage'],
                'help' => 'Choose an image',
                'data' => $options['data']->getMainImageFile(), // Pre-fill with the existing main image file name
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Publish Reel',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reel::class,
            'validation_groups' => ['create', 'update'],
        ]);
    }
}
