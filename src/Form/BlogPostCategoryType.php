<?php
    
    namespace App\Form;
    
    use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
    use App\Entity\BlogPostCategory;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
    class BlogPostCategoryType extends AbstractType
    {
        
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('translations', TranslationsType::class, [
                    // 'label' => 'Translation',
                    'fields' => [
                        'name' => [
                            'purify_html' => true,
                            'locale_options' => [
                                'en' => ['label' => 'Name'],
//                            'fr' => ['label' => 'Nom'],
//                            'es' => ['label' => 'Nombre'],
//                            'ar' => ['label' => 'اسم'],
//                            'pt' => ['label' => 'Nome'],
//                            'de' => ['label' => 'Name'],
                            ]
                        ]
                    ],
                    'excluded_fields' => ['slug']
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Save',
                    'attr' => ['class' => 'btn btn-primary'],
                ]);
        }
        
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => BlogPostCategory::class,
            ]);
        }
        
    }
