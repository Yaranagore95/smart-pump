<?php

namespace App\Form\SuperAdmin;

use App\Entity\StructureClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StructureClientAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom structure',
                'required' => true
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Logo structure',
                'required' => false
            ])
            ->add('adresse', TextType::class, [
                'required' => false
            ])
            ->add('users', CollectionType::class, [
                'entry_type' => UserClientAddType::class,
                'entry_options' => ['label' => false]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StructureClient::class,
        ]);
    }
}
