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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('icon', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Icon',
                'attr' => ['class' => 'icon-picker', 'autocomplete' => 'disabled']
            ])
            ->add('label', TextType::class, [
                'required' => false,
                'label' => 'label',
                'purify_html' => true,
            ])
            ->add('link', ChoiceType::class, [
                'required' => false,
                'label' => 'Choose the link destination page',
                'choices' => $this->services->getLinks(),
//                'attr' => ['class' => 'select2', 'data-sort-options' => '0']
            ])
            ->add('customLink', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Custom link',
            ])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'class' => 'menuelement-position',
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MenuElement::class,
        ]);
    }

}
