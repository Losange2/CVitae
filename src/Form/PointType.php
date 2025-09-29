<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Cv;
use App\Entity\Lieu;
use App\Entity\Point;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('le_cv', EntityType::class, [
                'class' => Cv::class,
                'choice_label' => 'id',
            ])
            ->add('la_cate', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'id',
            ])
            ->add('un_lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Point::class,
        ]);
    }
}
