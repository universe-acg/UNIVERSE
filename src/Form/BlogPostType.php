<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Form;
    
    use App\Entity\BlogPost;
    use App\Entity\BlogPostCategory;
    use App\Service\AppServices;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Vich\UploaderBundle\Form\Type\VichImageType;
    
    class BlogPostType extends AbstractType
    {
        private $services;
        
        public function __construct(AppServices $services)
        {
            $this->services = $services;
        }
        
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            
            $builder
                ->add('category', EntityType::class, [
                    'required' => true,
                    'constraints' => array(new NotBlank()),
                    'class' => BlogPostCategory::class,
                    'choice_label' => 'name',
                    'label' => 'Category',
                    'help' => 'Select the appropriate category to help users find your post.',
                    'placeholder' => 'Select a Category',
                    'query_builder' => function () {
                        return $this->services->getBlogPostCategoriesInForm(array());
                    },
                    'invalid_message' => 'Please select a category.',
                ])
                ->add('name', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Title',
                    'constraints' => array(new NotBlank()),
                    'help' => 'Enter a Title (e.g., "A Travel Adventure")',
                ])
                ->add('content', TextareaType::class, [
                    'required' => false,
                    'label' => 'Content',
                    'constraints' => array(new NotBlank()),
                    'attr' => ['class' => 'wysiwyg'],
                    'help' => 'Write your content here...',
                ])
                ->add('tags', TextType::class, [
                    'required' => false,
                    'label' => 'Tags',
                    'purify_html' => true,
                    'help' => 'Enter tags and press Enter',
                    'attr' => ['class' => 'tags-input'],
                ])
                ->add('imageFile', VichImageType::class, [
                    'required' => false,
                    'allow_delete' => true,
                    'download_label' => false,
                    'download_uri' => false,
                    'image_uri' => false,
                    'imagine_pattern' => 'scale',
                    'label' => 'Main post image',
                    
                    'attr' => ['placeholder' => 'Choose an Image'],
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Save',
                    'attr' => ['class' => 'btn btn-primary'],
                ]);
        }
        
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => BlogPost::class,
            ]);
        }
    }
