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
//            ->add('translations', TranslationsType::class, [
//                'required' => true,
//                'label' => 'Page content',
//                'fields' => [
//                    'title' => [
//                        'purify_html' => true,
//                        'locale_options' => [
//                            'en' => ['label' => 'Title'],
////                            'fr' => ['label' => 'Titre'],
////                            'es' => ['label' => 'Título'],
////                            'ar' => ['label' => 'عنوان'],
////                            'pt' => ['label' => 'Título'],
////                            'de' => ['label' => 'Titel'],
//                        ]
//                    ],
//                    'content' => [
//                        'purify_html' => true,
//                        'field_type' => TextareaType::class,
//                        'attr' => ['class' => 'wysiwyg'],
//                        'locale_options' => [
//                            'en' => ['label' => 'Content'],
////                                'fr' => ['label' => 'Contenu'],
////                                'es' => ['label' => 'Contenido'],
////                                'ar' => ['label' => 'محتوى'],
////                                'pt' => ['label' => 'Contente'],
////                                'de' => ['label' => 'Inhalt'],
//                        ]
//                    ],
//                ],
//                'excluded_fields' => ['slug']
//            ])
                ->add('title', TextType::class, [
                    'required' => false,
                    'label' => 'Title',
                    'purify_html' => true,
                ])
                ->add('content', TextareaType::class, [
                    'required' => false,
                    'label' => 'Content',
                    'purify_html' => true,
                    'attr' => ['class' => 'wysiwyg'],
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
