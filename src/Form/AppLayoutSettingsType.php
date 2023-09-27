<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\AppLayoutSettings;
use App\Service\AppServices;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppLayoutSettingsType extends AbstractType
{

    private $services;
    private $params;

    public function __construct(AppServices $services, ParameterBagInterface $params)
    {
        $this->services = $services;
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $pages = [];
        foreach ($this->services->getPages(array())->getQuery()->getResult() as $page) {
            $pages[$page->getTitle()] = $page->getSlug();
        }

        $builder
            ->add('app_environment', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'App Environment',
                'choices' => ['Production' => 'prod', 'Development' => 'dev'],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Select the application environment (Development environment is used for development purposes only)',
            ])
            ->add('app_debug', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'App Debugging',
                'choices' => ['Enable' => '1', 'Disable' => '0'],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Enable to display stack traces on error pages or if cache files should be dynamically rebuilt on each request',
            ])
            ->add('app_secret', TextType::class, [
                'purify_html' => true,
                'mapped' => false,
                'required' => true,
                'label' => 'App Secret',
                'constraints' => array(// Add validation constraints here if needed
                ),
                'help' => 'Enter a unique string used for security-related operations',
                'attr' => [
                    'placeholder' => 'Enter the app secret...',
                ],
            ])
            ->add('maintenance_mode', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Maintenance mode',
                'choices' => ['Disabled' => 0, 'Enabled' => 1],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => 'Enable maintenance mode to display a maintenance page for all users except those with the ROLE_ADMINISTRATOR role. If you lose your session, you can edit the MAINTENANCE_MODE parameter directly in the .env file',
            ])
            ->add('maintenance_mode_custom_message', TextareaType::class, [
                'purify_html' => true,
                'mapped' => false,
                'required' => false,
                'label' => 'Maintenance mode custom message',
                'help' => 'Enter a custom message to display during maintenance mode',
                'attr' => [
                    'placeholder' => 'Enter a custom message...',
                ],
            ])
            ->add('date_format', TextType::class, [
                'purify_html' => true,
                'mapped' => false,
                'required' => true,
                'label' => 'Date and time format',
                'constraints' => array(// Add validation constraints here if needed
                ),
                'help' => 'Enter the project-wide date and time format. Use supported characters; refer to <a href="https://unicode-org.github.io/icu/userguide/format_parse/datetime/">ICU User Guide</a> for details.',
                'attr' => [
                    'placeholder' => 'Enter the date and time format...',
                ],
            ])
            ->add('date_format_simple', TextType::class, [
                'purify_html' => true,
                'mapped' => false,
                'required' => true,
                'label' => 'Alternative date and time format',
                'constraints' => array(// Add validation constraints here if needed
                ),
                'help' => 'Enter an alternative date and time format for specific cases. Use supported characters; refer to <a href="https://www.php.net/manual/datetime.format.php">PHP DateTime Format</a> for details.',
                'attr' => [
                    'placeholder' => 'Enter the alternative date and time format...',
                ],
            ])
            ->add('date_format_date_only', TextType::class, [
                'purify_html' => true,
                'mapped' => false,
                'required' => true,
                'label' => 'Date only format',
                'constraints' => array(// Add validation constraints here if needed
                ),
                'help' => 'Enter a date-only format for specific cases. Use supported characters; refer to <a href="https://www.php.net/manual/datetime.format.php">PHP DateTime Format</a> for details.',
                'attr' => [
                    'placeholder' => 'Enter the date-only format...',
                ],
            ])
            ->add('date_timezone', TimezoneType::class, [
                'mapped' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'attr' => ['class' => 'select2', 'data-sort-options' => '1'],
                'label' => 'Timezone',
                'constraints' => array(// Add validation constraints here if needed
                ),
                'help' => 'Select the timezone for your application',
            ])
            // Continue adding fields with similar annotations
            // ...
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppLayoutSettings::class,
            'validation_groups' => ['create', 'update']
        ]);
    }

}
