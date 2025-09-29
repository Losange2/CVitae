<?php

namespace App\Form;

use App\Entity\Reseau;
use App\Entity\TypeDeReseau;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReseauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lien')
            ->add('proprio', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('le_type_r', EntityType::class, [
                'class' => TypeDeReseau::class,
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reseau::class,
        ]);
    }
}
