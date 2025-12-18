<?php

namespace App\Form;

use App\Entity\MaladiePoisson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MaladiePoissonForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('agentPathogene', TextType::class, [
                'label' => 'Agent pathogène',
                'required' => false,
            ])
            ->add('gravite', TextType::class, [
                'label' => 'Gravité',
                'required' => false,
            ])
            ->add('contagieuse', CheckboxType::class, [
                'label'    => 'Contagieuse',
                'required' => false,
            ])
            ->add('dureeTraitement', TextType::class, [
                'label' => 'Durée traitement',
                'required' => false,
            ])
            ->add('symptomes', TextareaType::class, [
                'label' => 'Symptômes',
                'required' => false,
            ])
            ->add('causes', TextareaType::class, [
                'label' => 'Causes',
                'required' => false,
            ])
            ->add('traitement', TextareaType::class, [
                'label' => 'Traitement',
                'required' => false,
            ])
            ->add('prevention', TextareaType::class, [
                'label' => 'Prévention',
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Bactérie' => 'Bactérie',
                    'Parasite' => 'Parasite',
                    'Virus'    => 'Virus',
                    'Champignon' => 'Champignon',
                    'Autre'    => 'Autre',
                ],
                'placeholder' => 'Non précisé',
            ])
            // Champ de fichier pour l’image
            ->add('imageFile', FileType::class, [
                'label' => 'Image de la maladie (facultatif)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci d\'ajouter une image JPG, PNG ou WEBP valide.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaladiePoisson::class,
        ]);
    }
}

