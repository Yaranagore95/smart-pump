<?php

namespace App\Form;

use App\Entity\Stockage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockageAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'required' => true
            ])
            ->add('prixUnitaire', IntegerType::class, [
                'label' => 'Prix Achat',
                'required' => true
            ])
            ->add('manquant', IntegerType::class, [
                'label' => 'Manquant',
                'required' => true,
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stockage::class,
        ]);
    }
}
