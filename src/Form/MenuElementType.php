<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\MenuElement;
use App\Service\AppServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuElementType extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('icon', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Icon (optional)',
                'attr' => ['class' => 'icon-picker', 'autocomplete' => 'disabled'],
                'help' => 'Enter the icon class name for the menu element (optional).'
            ])
            ->add('label', TextType::class, [
                'required' => false,
                'label' => 'Label (optional)',
                'purify_html' => true,
                'attr' => ['placeholder' => 'Enter menu label, e.g., "Home"'],
                'help' => 'Enter the label for the menu element (optional).'
            ])
            ->add('link', ChoiceType::class, [
                'required' => false,
                'label' => 'Link Destination Page',
                'choices' => $this->services->getLinks(),
                'attr' => ['class' => 'select2', 'data-sort-options' => '0'],
                'placeholder' => 'Select a page or leave empty',
                'help' => 'Choose the destination page for the menu element.'
            ])
            ->add('customLink', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Custom Link (optional)',
                'attr' => ['placeholder' => 'Enter a custom URL for the menu element'],
                'help' => 'Enter a custom URL for the menu element (optional).'
            ])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'class' => 'menuelement-position',
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuElement::class,
        ]);
    }

}
