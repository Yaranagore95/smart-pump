<?php

namespace App\Form;

use App\Entity\Cuve;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CuveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('capacity', IntegerType::class, [
                'label' => 'Capacité de la cuve', 'required' => true
            ])
            ->add('qteAlerte', IntegerType::class, [
                'label' => 'Quantité alerte',
                'required' => true
            ])
            ->add('description', TextType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cuve::class,
        ]);
    }
}
