<?php

namespace App\Form;

use App\Entity\Espece;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EspeceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCommun', TextType::class, [
                'label' => 'Nom commun',
            ])
            ->add('nomScientifique', TextType::class, [
                'label' => 'Nom scientifique',
            ])
            ->add('classification', TextareaType::class, [
                'label' => 'Classification',
                'required' => false,
            ])
            ->add('origine', TextareaType::class, [
                'label' => 'Origine',
                'required' => false,
            ])
            ->add('repartitionGeographique', TextType::class, [
                'label' => 'Répartition géographique',
                'required' => false,
            ])
            ->add('descriptionPhysique', TextareaType::class, [
                'label' => 'Description physique',
                'required' => false,
            ])
            ->add('dimorphismeSexuel', TextareaType::class, [
                'label' => 'Dimorphisme sexuel',
                'required' => false,
            ])
            ->add('alimentation', TextareaType::class, [
                'label' => 'Alimentation',
                'required' => false,
            ])
            ->add('tailleMinimaleBac', NumberType::class, [
                'label' => 'Taille minimale du bac (en litres)',
                'required' => false,
            ])
            ->add('temperatureMin', NumberType::class, [
                'label' => 'Température min (°C)',
                'required' => false,
                'scale' => 1,
            ])
            ->add('temperatureMax', NumberType::class, [
                'label' => 'Température max (°C)',
                'required' => false,
                'scale' => 1,
            ])
            ->add('phMin', NumberType::class, [
                'label' => 'pH min',
                'required' => false,
                'scale' => 1,
            ])
            ->add('phMax', NumberType::class, [
                'label' => 'pH max',
                'required' => false,
                'scale' => 1,
            ])
            ->add('ghMin', NumberType::class, [
                'label' => 'GH min',
                'required' => false,
                'scale' => 1,
            ])
            ->add('ghMax', NumberType::class, [
                'label' => 'GH max',
                'required' => false,
                'scale' => 1,
            ])
            ->add('comportement', TextareaType::class, [
                'label' => 'Comportement',
                'required' => false,
            ])
            ->add('reproduction', TextareaType::class, [
                'label' => 'Reproduction',
                'required' => false,
            ])
            ->add('typeEspece', ChoiceType::class, [
                'label' => 'Type d\'espèce',
                'choices' => [
                    'Poisson' => 'poisson',
                    'Plante' => 'plante',
                    'Mollusque' => 'mollusque',
                    'Crustacé' => 'crustacé',
                ],
                'placeholder' => '-- Choisir --',
            ])
            ->add('biotope', TextType::class, [
                'label' => 'Biotope',
                'required' => false,
            ])
            ->add('dureeVie', TextType::class, [
                'label' => 'Durée de vie',
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image de l\'espèce',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Merci d\'envoyer une image valide (JPEG, PNG ou WEBP)',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Espece::class,
        ]);
    }
}


