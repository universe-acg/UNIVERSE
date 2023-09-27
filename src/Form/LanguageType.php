<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'Language Name',
                'purify_html' => true,
                'attr' => ['placeholder' => 'Enter language name (optional)'],
                'help' => 'Enter the name of the language (optional).'
            ])
            ->add('code', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Language Code',
                'attr' => ['placeholder' => 'e.g., en_US'],
                'help' => 'Enter the language code, e.g., en_US.'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Language::class,
        ]);
    }

}
