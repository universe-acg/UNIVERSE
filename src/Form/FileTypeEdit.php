<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */

    namespace App\Form;

    use App\Entity\File;
    use App\Service\AppServices;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Vich\UploaderBundle\Form\Type\VichImageType;

    class FileTypeEdit extends AbstractType
    {
        private $services;

        private $security;

        public function __construct(AppServices $services, Security $security)
        {
            $this->services = $services;
            $this->security = $security;
        }

        public function buildForm(FormBuilderInterface $builder, array $options): void
    {

            $builder
                ->add('title', TextType::class, [
                    'purify_html' => true,
                    'required' => false,
                    'label' => 'Title of document',
                    'constraints' => array(new NotBlank()),
                    'help' => 'Enter a Title (e.g., "Budget sheet Spring 2023-2024")',
                ])
                ->add('currentFile', TextType::class, [
                    'mapped' => false, // This field is not mapped to an entity property
                    'required' => false,
                    'label' => 'Current File',
                    'disabled' => true, // Disable the input field
                ])
                ->add('fileFile', VichImageType::class, [
                    'required' => false,
                    'allow_delete' => true,
                    'download_label' => false,
                    'download_uri' => false,
                    'label' => 'File',
                    'attr' => ['placeholder' => 'Upload a file'],
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Save',
                    'attr' => ['class' => 'btn btn-primary'],
                ]);
        }

        public function configureOptions(OptionsResolver $resolver): void
    {
            $resolver->setDefaults([
                'data_class' => File::class,
            ]);
        }
    }
