<?php

namespace App\Form;

use App\Entity\Plante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanteForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCommun')
            ->add('nomScientifique')
            ->add('famille')
            ->add('origine')
            ->add('description')
            ->add('eclairage')
            ->add('croissance')
            ->add('hauteurMax')
            ->add('positionAquarium')
            ->add('difficulte')
            ->add('phMin')
            ->add('phMax')
            ->add('tempMin')
            ->add('tempMax')
            ->add('image')
            ->add('no')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plante::class,
        ]);
    }
}
