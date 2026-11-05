<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\HelpCenterArticle;
use App\Entity\HelpCenterCategory;
use App\Service\AppServices;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpCenterArticleType extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'required' => true,
                'multiple' => false,
                'attr' => ['class' => 'select2'],
                'class' => HelpCenterCategory::class,
                'choice_label' => 'name',
                'label' => 'Category',
                'help' => 'Make sure to select right category to let the users find it quickly',
                'query_builder' => function () {
                    return $this->services->getHelpCenterCategories(array());
                },
            ])
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Title',
                'purify_html' => true,
            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'label' => 'Content',
                'attr' => ['class' => 'wysiwyg'],
            ])
//            ->add('tags', TextType::class, [
//                'required' => false,
//                'label' => 'Tags',
//                'attr' => ['class' => 'tags-input'],
//            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HelpCenterArticle::class,
        ]);
    }
}
