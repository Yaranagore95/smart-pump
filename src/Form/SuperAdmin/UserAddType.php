<?php

namespace App\Form\SuperAdmin;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'label' => 'Mot de passe',
                'invalid_message' => 'Ce champ doit être identique à la confirmation',
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse émail',
                'required' => true
            ])
            ->add('nom', TextType::class, [
                'required' => true
            ])
            ->add('prenom', TextType::class, [
                'required' => false,
                'label' => 'Prénom'
            ])
            ->add('telephone', TextType::class, [
                'required' => true,
                'label' => 'Téléphone'
            ])
            ->add('adresse', TextType::class, [
                'required' => false
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Photo profile',
                'required' => false
            ])
            ->add('ENREGISTRER', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark btn-sm font-weight-bold text-uppercase float-right']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
