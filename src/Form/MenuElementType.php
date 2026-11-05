<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Form;

use App\Entity\MenuElement;
use App\Service\AppServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuElementType extends AbstractType
{

    private $services;

    public function __construct(AppServices $services)
    {
        $this->services = $services;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices=$this->services->getLinks();
        $values=[];array_walk_recursive($choices,static function($value)use(&$values){$values[]=$value;});
        $builder
            ->add('icon', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Icon (optional)',
                'attr' => ['class' => 'icon-picker', 'autocomplete' => 'disabled'],
                'help' => 'Enter the icon class name for the menu element (optional).'
            ])
            ->add('label', TextType::class, [
                'required' => true,
                'label' => 'Label',
                'purify_html' => true,
                'attr' => ['placeholder' => 'Enter menu label, e.g., "Home"'],
                'help' => 'Text displayed for this menu link.'
            ])
            ->add('link', ChoiceType::class, [
                'required' => false,
                'label' => 'Link Destination Page',
                'choices' => $choices,
                'attr' => ['class' => 'select2', 'data-sort-options' => '0'],
                'placeholder' => 'Select a page or leave empty',
                'help' => 'Choose a destination, or leave this empty to use a custom link.'
            ])
            ->add('customLink', TextType::class, [
                'purify_html' => true,
                'required' => false,
                'label' => 'Custom Link (optional)',
                'attr' => ['placeholder' => 'Enter a custom URL for the menu element'],
                'help' => 'Use a site path such as /events, or a full URL. Leave the page selection empty.'
            ])
            ->add('audience', ChoiceType::class, ['label'=>'Visible to','choices'=>['Everyone'=>'everyone','Guests'=>'guests','Signed-in users'=>'authenticated','Attendees'=>'attendees','Organizers'=>'organizers','Administrators'=>'administrators']])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'class' => 'menuelement-position',
                ]]);


        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function(FormEvent $event) use ($choices,$values): void {
            $element=$event->getData();$current=$element instanceof MenuElement ? $element->getLink() : null;
            if(!$current || in_array($current,$values,true))return;
            $selectedChoices=$choices;$selectedChoices['Current destination']=['Keep current destination'=>$current];
            $event->getForm()->add('link',ChoiceType::class,['required'=>false,'label'=>'Link Destination Page','choices'=>$selectedChoices,'attr'=>['class'=>'select2','data-sort-options'=>'0'],'placeholder'=>'Select a page or leave empty','help'=>'Choose a destination, or leave this empty to use a custom link.']);
        });
        if($options['in_header'])$builder->add('placement',ChoiceType::class,['label'=>'Placement','choices'=>['Main navigation'=>'primary','More dropdown'=>'more']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuElement::class,
            'in_header' => false,
        ]);
    }

}
