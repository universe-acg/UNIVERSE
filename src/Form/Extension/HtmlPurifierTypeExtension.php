<?php
namespace App\Form\Extension;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
/** Preserve the legacy form option while using maintained HTMLPurifier directly. */
final class HtmlPurifierTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable { return [TextType::class]; }
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['purify_html'=>false,'purify_html_profile'=>null]);$resolver->setAllowedTypes('purify_html','bool'); }
    public function buildForm(FormBuilderInterface $builder,array $options): void {
        if(!$options['purify_html'])return;
        $builder->addEventListener(FormEvents::PRE_SUBMIT,static function(FormEvent $event):void {
            if(!is_string($event->getData()))return;
            $config=\HTMLPurifier_Config::createDefault();$config->set('Cache.DefinitionImpl',null);
            $event->setData((new \HTMLPurifier($config))->purify($event->getData()));
        });
    }
}
