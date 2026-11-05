<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\EventFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class EventFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eventfile', VichFileType::class, [
                'required' => true,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'label' => 'File',
                'help' => 'Choose a file for your event',
                'invalid_message' => 'Invalid file format. Please upload a valid file.',
                'attr' => [
                    'class' => 'custom-file-input', // Add a class for identifying the file input
                ],
            ])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'class' => 'form-collection-position',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventFile::class,
            'validation_groups' => ['create', 'update'],
        ]);
    }
}
