<?php

namespace App\Form;

use App\Entity\Plante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PlanteForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCommun')
            ->add('imageFile', FileType::class, [
                'label' => 'Image (JPG/PNG/WebP)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Formats acceptés : JPG, PNG, WebP',
                    ])
                ],
            ])
            ->add('nomScientifique')
            ->add('famille')
            ->add('origine')
            ->add('biotope', ChoiceType::class, [
                'required' => false,
                'placeholder' => '— Choisir un biotope —',
                'choices' => [
                    'Amérique du sud'      => 'amerique-sud',
                    'Amérique centrale'    => 'amerique-centrale',
                    'Asie du Sud-Est'      => 'asiatique',
                    'Afrique'              => 'africain',
                    'Australie'            => 'australien',
                    'Europe'               => 'europeen',
                    'Eaux saumâtres'       => 'eaux-saumatres',
                    'Mangroves'            => 'mangroves',
                    'Autre'                => 'autre',
                ],
            ])
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

