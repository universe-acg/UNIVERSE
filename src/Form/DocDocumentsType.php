<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\DocDocumentDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DocDocumentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('docfile1', VichFileType::class, [
                'label' => 'Doctor\'s File I',
                'required' => false,
                'allow_delete' => false,
                'attr' => [
                    'placeholder' => $options['data']->getFileOriginalName() ?: 'Select File',
                    'class' => 'custom-file-input',
                ],
                'help' => 'Upload a PDF or Word document.',
                'download_uri' => true,
                'data' => null,
            ])
            ->add('deletedocfile1', CheckboxType::class, [
                'label' => 'Delete File',
                'required' => false,
            ])
            ->add('docfile2', VichFileType::class, [
                'label' => 'Doctor\'s File II',
                'required' => false,
                'allow_delete' => false,
                'attr' => [
                    'placeholder' => $options['data']->getFileOriginalName1() ?: 'Select File',
                    'class' => 'custom-file-input',
                ],
                'help' => 'Upload a PDF or Word document.',
                'download_uri' => true,
            ])
            ->add('deletedocfile2', CheckboxType::class, [
                'label' => 'Delete File',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocDocumentDTO::class,
            'validation_groups' => ['create', 'update'],
        ]);
    }
}
