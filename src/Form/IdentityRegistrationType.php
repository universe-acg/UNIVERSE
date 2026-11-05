<?php
namespace App\Form;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
class IdentityRegistrationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class'=>User::class]); }
}
