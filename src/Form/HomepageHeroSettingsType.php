<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\Event;
use App\Entity\HomepageHeroSettings;
use App\Entity\User;
use App\Service\AppServices;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class HomepageHeroSettingsType extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('content', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'What to show in the homepage hero ?',
                'choices' => ['Hide slider' => 'none', 'Events slider' => 'events', 'Organizers slider' => 'organizers', 'Custom hero' => 'custom'],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'constraints' => array(// new NotBlank()
                ),
            ])
            ->add('events', EntityType::class, [
                'required' => false,
                'multiple' => true,
                'class' => Event::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'select2'],
                'label' => 'Events',
                'query_builder' => function () {
                    return $this->services->getEvents(array("elapsed" => "all"));
                }
            ])
            ->add('organizers', EntityType::class, [
                'required' => false,
                'multiple' => true,
                'class' => User::class,
                'choice_label' => 'organizer.name',
                'attr' => ['class' => 'select2'],
                'label' => 'Organizers',
                'help' => 'Make sure to select organizers who have added a cover photo',
                'query_builder' => function () {
                    return $this->services->getUsers(array('role' => 'organizer'));
                }
            ])
            ->add('customBackgroundFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => 'Custom hero background image',
            ])
            ->add('title', TextType::class, [
                'required' => false,
                'label' => $options['title_label'],
                'purify_html' => true,
            ])
            ->add('paragraph', TextType::class, [
                'required' => false,
                'label' => $options['paragraph_label'],
                'purify_html' => true,
            ])
            ->add('show_search_box', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Show the homepage hero seach box',
                'choices' => ['Yes' => true, 'No' => false],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
            ])
            // Fields below are not stored in the entity, but in the settings cache
            ->add('homepage_show_search_box', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Show the search box',
                'choices' => ['Yes' => 'yes', 'No' => 'no'],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
//                    'constraints' => array(
//                        new NotNull()
//                    ),
            ])
            ->add('homepage_events_number', TextType::class, [
                'purify_html' => true,
                'mapped' => false,
                'required' => true,
                'label' => 'Number of events to show',
                'attr' => ['class' => 'touchspin-integer', 'data-min' => 0, "data-max" => 36]
            ])
            ->add('homepage_categories_number', TextType::class, [
                'purify_html' => true,
                'mapped' => false,
                'required' => true,
                'label' => 'Number of featured categories to show',
                'attr' => ['class' => 'touchspin-integer', 'data-min' => 0, "data-max" => 21]
            ])
            ->add('homepage_blogposts_number', TextType::class, [
                'purify_html' => true,
                'mapped' => false,
                'required' => true,
                'label' => 'Number of blog posts to show',
                'attr' => ['class' => 'touchspin-integer', 'data-min' => 0, "data-max" => 15]
            ])
            ->add('homepage_show_call_to_action', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'label' => 'Show the call to action block',
                'choices' => ['Yes' => 'yes', 'No' => 'no'],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
//                    'constraints' => array(
//                        new NotNull()
//                    ),
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HomepageHeroSettings::class
        ]);
    }

}
