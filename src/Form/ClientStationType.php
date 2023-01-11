<?php

namespace App\Form;

use App\Entity\ClientStation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientStationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => 'true'
            ])
            ->add('prenom', TextType::class, [
                'required' => false
            ])
            ->add('telephone', TextType::class, [
                'required' => false
            ])
            ->add('adresse', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClientStation::class,
        ]);
    }
}
