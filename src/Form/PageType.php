<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Page Title',
                'purify_html' => true,
                'attr' => [
                    'placeholder' => 'Enter the page title',
                    'help' => 'Provide a descriptive title for the page.',
                ],
            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'label' => 'Page Content',
                'purify_html' => true,
                'attr' => [
                    'class' => 'wysiwyg',
                    'placeholder' => 'Enter the page content',
                    'help' => 'Add the content for the page. You can use rich text formatting.',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
