<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\ListingImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ListingImageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => 'Gallery Image',
                'help' => 'Choose an image for your listing gallery',
                'attr' => [
                    'placeholder' => 'Upload an image...',
                ],
                'invalid_message' => 'Invalid image format. Please upload a valid image.',
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
            'data_class' => ListingImage::class,
            'validation_groups' => ['create', 'update']
        ]);
    }

}
