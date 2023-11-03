<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\VenueImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class VenueImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => 'Upload Venue Image',
                'attr' => [
                    'placeholder' => 'Choose an image for the venue',
                    'help' => 'Upload an image to represent the venue. Supported formats: JPEG, PNG.',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M', // Set the maximum upload size (e.g., 5 megabytes)
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG).',
                    ]),
                ],
            ])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'class' => 'form-collection-position',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VenueImage::class,
        ]);
    }
}
