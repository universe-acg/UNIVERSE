<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\User;
use App\Service\AppServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AccountSettingsType extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//                ->add('email', EmailType::class, [
//                    'purify_html' => true,
//                    'required' => true,
//                    'label' => 'Email',
//                    'help' => 'Enter your email address',
//                    'constraints' => [
//                        new AcgEmailDomain([
//                            'message' => 'Invalid email domain. Please use an @acg.edu email address.',
//                        ]),
////                    new SymfonyEmail([
////                        'message' => 'Please enter a valid email address.'
////                    ]),
//                        new NotBlank()
//                    ],
//                    'attr' => [
//                        'readonly' => true,
//                        'placeholder' => 'example@example.com',
//                    ]
//                ])
//                ->add('username', TextType::class, [
//                    'purify_html' => true,
//                    'required' => true,
//                    'label' => 'Username',
//                    'help' => 'Enter your username',
//                    'constraints' => [
//                        new Length([
//                            'min' => 2,
//                            'max' => 30,
//                            'minMessage' => 'Username should be at least {{ limit }} characters long.',
//                            'maxMessage' => 'Username should be no more than {{ limit }} characters long.'
//                        ])
//                    ],
//                    'attr' => [
//                        'readonly' => true,
//                        'placeholder' => 'john_doe123',
//                    ]
//                ])
            ->add('logoFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => 'Profile image',
                'help' => 'Upload a profile image. Supported formats: PNG, JPEG (max size: 5MB).',
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                    ]),
                ],
            ])
            ->add('selectedImage', ChoiceType::class, [
                'label' => 'Select a default profile image',
                'choices' => [
                    'Icon 1' => 'photoprofiles_icons-01.png',
                    'Icon 2' => 'photoprofiles_icons-02.png',
                    'Icon 3' => 'photoprofiles_icons-03.png',
                    'Icon 4' => 'photoprofiles_icons-04.png',
                    'Icon 5' => 'photoprofiles_icons-05.png',
                    'Icon 7' => 'photoprofiles_icons-07.png',
                    'Icon 8' => 'photoprofiles_icons-08.png',
                    'Icon 9' => 'photoprofiles_icons-09.png',
                ],
                'required' => false,
                'expanded' => true,  // Render as radio buttons
            ])
            ->add('coverFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => 'Cover photo',
                'help' => 'Upload a cover photo for your profile background. Supported formats: PNG, JPEG (max size: 5MB).',
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'First name',
                'help' => 'Enter your first name',
                'attr' => [
                    'placeholder' => 'John',
                ]
            ])
            ->add('lastname', TextType::class, [
                'purify_html' => true,
                'required' => true,
                'label' => 'Last name',
                'help' => 'Enter your last name',
                'attr' => [
                    'placeholder' => 'Doe',
                ]
            ])
            ->add('phone', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Phone number',
                'help' => 'Enter your phone number (optional)',
                'attr' => [
                    'placeholder' => '123-456-7890',
                ]
            ])
            ->add('birthdate', DateType::class, [
                'required' => false,
                'label' => 'Birthdate',
                'help' => 'Select your birthdate (optional)',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'placeholder' => 'YYYY-MM-DD',
                ]
            ])
            ->add('website', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Website',
                'help' => 'Enter your website URL (optional)',
                'attr' => [
                    'placeholder' => 'https://www.example.com',
                ]
            ])
            ->add('twitter', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Twitter',
                'help' => 'Enter your Twitter handle (optional)',
                'attr' => [
                    'placeholder' => '@example',
                ]
            ])
            ->add('instagram', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Instagram',
                'help' => 'Enter your Instagram username (optional)',
                'attr' => [
                    'placeholder' => 'example',
                ]
            ])
            ->add('facebook', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Facebook',
                'help' => 'Enter your Facebook profile URL (optional)',
                'attr' => [
                    'placeholder' => 'https://www.facebook.com/example',
                ]
            ])
            ->add('linkedin', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'LinkedIn',
                'help' => 'Enter your LinkedIn profile URL (optional)',
                'attr' => [
                    'placeholder' => 'https://www.linkedin.com/in/example',
                ]
            ])
            ->add('bio', TextareaType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Bio',
                'help' => 'Write a short bio about yourself (optional)',
                'attr' => [
                    'placeholder' => 'Tell us about yourself...',
                ]
            ])
            ->add('showattendeed', ChoiceType::class, [
                'required' => false,
                'label' => 'Show Attended Events',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => false,
                'expanded' => true,
                'help' => 'Select Yes show attendees events publicly on your profile',
                'multiple' => false,
                'attr' => [
                    'class' => 'custom-switch-input', // You can add custom CSS classes if needed
                ],
            ])
            ->add('showfavs', ChoiceType::class, [
                'required' => false,
                'empty_data' => false,
                'label' => 'Show Favorite Events',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => false,
                'expanded' => true,
                'help' => 'Select Yes show favorites events publicly on your profile',
                'multiple' => false,
                'attr' => [
                    'class' => 'custom-switch-input', // You can add custom CSS classes if needed
                ],
            ])
//                ->add('showcomments', ChoiceType::class, [
//                    'required' => false,
//                    'label' => 'Show Comments',
//                    'choices' => [
//                        'Yes' => true,
//                        'No' => false,
//                    ],
//                    'placeholder' => false,
//                    'expanded' => true,
//                    'help' => 'Select Yes show comments publicly on your profile',
//                    'multiple' => false,
//                    'attr' => [
//                        'class' => 'custom-switch-input', // You can add custom CSS classes if needed
//                    ],
//                ])
            ->add('getdailyreminders', ChoiceType::class, [
                'required' => false,
                'empty_data' => true,
                'label' => 'Do you want to get reminded by email when an event is soon to start?',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => false,
                'expanded' => true,
                'help' => 'You will get reminded at least 24 hours prior',
                'multiple' => false,
                'attr' => [
                    'class' => 'custom-switch-input', // You can add custom CSS classes if needed
                ],
            ])
            ->add('getcreatorreminder', ChoiceType::class, [
                'required' => false,
                'empty_data' => true,
                'label' => 'Do you want to get informed by email when important event changes take place?',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => false,
                'expanded' => true,
                'help' => 'Such important event changes are: event cancellations, start date and time changes, etc...',
                'multiple' => false,
                'attr' => [
                    'class' => 'custom-switch-input', // You can add custom CSS classes if needed
                ],
            ])
            ->add('nolevel', ChoiceType::class, [
                'required' => false,
                'empty_data' => true,
                'label' => 'Do you want to remove the badges/level system from your profile?',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => false,
                'expanded' => true,
                'help' => 'If yes, this will remove the icon appearing next to your profile image',
                'multiple' => false,
                'attr' => [
                    'class' => 'custom-switch-input', // You can add custom CSS classes if needed
                ],
            ])
            ->add('sendsms', ChoiceType::class, [
                'required' => false,
                'empty_data' => true,
                'label' => 'Do you want to receive SMS notifications?',
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => false,
                'expanded' => true,
                'help' => 'You will receive the relevant notifications according to your preferences with SMS to the phone number set above',
                'multiple' => false,
                'attr' => [
                    'class' => 'custom-switch-input', // You can add custom CSS classes if needed
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
