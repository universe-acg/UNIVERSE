<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Form;
    
    use App\Entity\ListingSubmission;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Security\Core\Security;
    use Vich\UploaderBundle\Form\Type\VichFileType;
    
    class ListingSubmissionType extends AbstractType
    {
        private $security;
        
        public function __construct(Security $security)
        {
            $this->security = $security;
        }
        
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $userCvNotEmpty = !empty($this->security->getUser()->getCv());
            
            $builder
                ->add('content', TextareaType::class, [
                    'required' => false,
                    'label' => 'Message',
                    'attr' => [
                        'rows' => 3,
                        'placeholder' => 'I am excited about this opportunity because...',
                        'class' => 'mb form-control',
                    ],
                ])
                ->add('submitdefaultcv', ChoiceType::class, [
                    'required' => true,
                    'label' => 'Upload your default CV?',
                    'choices' => [
                        'Yes' => true,
                        'No' => false,
                    ],
                    'data' => $userCvNotEmpty, // Set default value based on condition
                    'expanded' => true, // Display as radio buttons
                    'multiple' => false, // Allow only one selection
                    'label_attr' => [
                        'title' => 'If true, then your CV uploaded will be used in this listing',
                        'class' => 'repeats-weekly-field radio-custom radio-inline',
                    ],
                    'mapped' => false, // Since it's not a property of the ListingSubmission entity
                ])
                ->add('fileFile', VichFileType::class, [
                    'label' => 'File',
                    'attr' => [
                        'accept' => '.pdf, .doc, .docx',
                        'title' => 'Attach CV or cover letter', // <-- Added title attribute
                        'placeholder' => 'No file chosen', // <-- Added placeholder attribute
                        'class' => 'form-control',
                    ],
                    'required' => false,
                ])
                ->add('apply', SubmitType::class, [
                    'label' => 'Apply',
                    'attr' => [
                        'class' => 'btn btn-primary btn-block',
                        'style' => 'border-radius: 0.25em',
                    ],
                ]);
        }
        
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => ListingSubmission::class,
                'validation_groups' => ['create', 'update'],
            ]);
        }
    }
