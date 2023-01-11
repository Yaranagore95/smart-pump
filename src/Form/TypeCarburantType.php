<?php

namespace App\Form;

use App\Entity\TypeCarburant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeCarburantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du type',
                'required' => true
            ])
            ->add('description', TextType::class, [
                'required' => false
            ])
            ->add('prixLittre', IntegerType::class, [
                'label' => 'Prix de vente / littre'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TypeCarburant::class,
        ]);
    }
}
