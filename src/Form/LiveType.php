<?php
namespace App\Form;
use App\Entity\Live;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{TextType,TextareaType,ChoiceType,CheckboxType,SubmitType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
final class LiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder,array $options): void
    {
        $builder->add('title',TextType::class,['purify_html'=>true,'label'=>'Livestream Title'])
            ->add('description',TextareaType::class,['purify_html'=>true,'label'=>'Description','attr'=>['class'=>'wysiwyg']])
            ->add('link',TextType::class,['required'=>false,'label'=>'Stream or preview URL','help'=>'Use an HTTPS YouTube, Vimeo or Twitch link, or a local video path.'])
            ->add('status',ChoiceType::class,['choices'=>['Preview'=>0,'Upcoming'=>1,'Live'=>2,'Ended'=>3]])
            ->add('hidden',CheckboxType::class,['required'=>false,'label'=>'Hide from the public list'])
            ->add('mainImageFile',VichImageType::class,['required'=>false,'allow_delete'=>false,'download_uri'=>false,'image_uri'=>false,'label'=>'Cover image'])
            ->add('save',SubmitType::class,['label'=>'Save Livestream','attr'=>['class'=>'btn btn-primary']]);
    }
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class'=>Live::class]); }
}
