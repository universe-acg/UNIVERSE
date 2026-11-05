<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\CV;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class CVType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cvfile', VichFileType::class, [
                'label' => 'CV File',
                'required' => false,
                'allow_delete' => false, // Prevent the "Delete" checkbox
                'attr' => [
                    'placeholder' => $options['data']->getFileOriginalName() ?: 'Select File',
                    'class' => 'custom-file-input', // Add a class for identifying the file input
                ], // Add a placeholder
                'help' => 'Upload a PDF or Word document.', // Add a help/hint message
                'download_uri' => true, // Allow downloading the existing file
            ])
            ->add('deleteCvfile', CheckboxType::class, [
                'label' => 'Delete CV File',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CV::class,
            'validation_groups' => ['create', 'update'],
        ]);
    }
}
