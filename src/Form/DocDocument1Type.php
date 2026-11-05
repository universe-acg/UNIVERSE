<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\DocDocument1;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DocDocument1Type extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('docfile', VichFileType::class, [
                'label' => 'Doctor\'s File I',
                'required' => false,
                'allow_delete' => false, // Prevent the "Delete" checkbox
                'attr' => ['placeholder' => 'Choose a file...',
                    'class' => 'custom-file-input', // Add a class for identifying the file input
                ], // Add a placeholder
                'help' => 'Upload a PDF or Word document.', // Add a help/hint message
                'download_uri' => true, // Allow downloading the existing file
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Upload Document',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DocDocument1::class,
            'validation_groups' => ['create', 'update'],
        ]);
    }
}
