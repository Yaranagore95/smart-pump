<?php

namespace App\Form;

use App\Entity\GlobalStockage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GlobalStockageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantite', NumberType::class, [
                'required' => true,
            ])
            ->add('prixAchat', IntegerType::class, [
                'required' => true,
                'label' => 'Prix achat littre'
            ])
            ->add('manquant', IntegerType::class, [
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GlobalStockage::class,
        ]);
    }
}
