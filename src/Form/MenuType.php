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
//                ->add('translations', TranslationsType::class, [
//                    'required' => false,
//                    // 'label' => 'Translation',
//                    'fields' => [
//                        'name' => [
//                            'purify_html' => true,
//                            'locale_options' => [
//                                'en' => ['label' => 'Menu name'],
////                            'fr' => ['label' => 'Nom du menu'],
////                            'es' => ['label' => 'Nombre du menú'],
////                            'ar' => ['label' => 'اسم القائمة'],
////                            'pt' => ['label' => 'Nome do menu'],
////                            'de' => ['label' => 'Menüname'],
//                            ]
//                        ],
//                        'header' => [
//                            'purify_html' => true,
//                            'locale_options' => [
//                                'en' => ['label' => 'Header text'],
////                            'fr' => ['label' => 'En-tête'],
////                            'es' => ['label' => 'En cabeza'],
////                            'ar' => ['label' => 'نص العنوان'],
////                            'pt' => ['label' => 'Na sua mente'],
////                            'de' => ['label' => 'In deinen Gedanken'],
//                            ]
//                        ]
//                    ],
//                    'excluded_fields' => ['slug']
//                ])
                ->add('name', TextType::class, [
                    'required' => false,
                    'label' => 'Menu name',
                    'purify_html' => true,
                ])
                ->add('header', TextType::class, [
                    'required' => false,
                    'label' => 'Header text',
                    'purify_html' => true,
                ])
                ->add('menuElements', CollectionType::class, array(
                    'label' => 'Menu elements',
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
