<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Menu Name',
                'purify_html' => true,
                'attr' => ['placeholder' => 'Enter menu name'],
                'help' => 'Enter the name for the menu.'
            ])
            ->add('menuElements', CollectionType::class, array(
                'label' => 'Menu Elements',
                'entry_type' => MenuElementType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__menuelements__',
                'required' => true,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'menuelements-collection form-collection manual-init',
                ),
                'help' => 'Add menu elements to the menu.',
            ))
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }

}
