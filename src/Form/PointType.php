<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Cv;
use App\Entity\Lieu;
use App\Entity\Point;
use App\Repository\CvRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointType extends AbstractType
{
    private CvRepository $cvRepository;

    public function __construct(CvRepository $cvRepository)
    {
        $this->cvRepository = $cvRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Ajout des champs cachés par défaut
        $builder
            ->add('le_cv', EntityType::class, [
                'class' => Cv::class,
                'choice_label' => 'Titre',
                'label' => false,
                'attr' => ['style' => 'display:none;'],
                'disabled' => true,
            ])
            ->add('la_cate', EntityType::class, [
            'class' => Categorie::class,
            'choice_label' => 'libelle',
            'attr' => ['style' => 'display:none;'],
            'label' => false,
            'disabled' => true,
            ])
            ->add('libelle', null, [
            ]);


        // Affichage conditionnel si le paramètre de catégorie est rempli
        if (!empty($options['categorie_param'])) {
            $builder->get('le_cv')->setAttribute('style', '');
            $builder->get('la_cate')->setAttribute('style', '');
            $builder->get('libelle')->setAttribute('style', '');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Point::class,
        ]);
    }
}
