<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\HelpCenterCategory;
use App\Service\AppServices;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpCenterCategoryType extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('parent', EntityType::class, [
                'required' => false,
                'multiple' => false,
                'class' => HelpCenterCategory::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'select2'],
                'label' => 'Parent Category',
                'help' => 'Select the parent category if this is a sub-category.',
                'query_builder' => function () {
                    return $this->services->getHelpCenterCategories(array("parent" => "none"));
                },
                'placeholder' => 'Select parent category (optional)',
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'Category Name',
                'purify_html' => true,
                'attr' => ['placeholder' => 'Enter category name'],
            ])
            ->add('icon', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Icon',
                'attr' => [
                    'class' => 'icon-picker',
                    'autocomplete' => 'disabled',
                    'placeholder' => 'Enter icon name or URL',
                ],
                'help' => 'Enter the icon name or URL for the category (optional).',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HelpCenterCategory::class,
        ]);
    }

}
