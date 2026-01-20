<?php

namespace App\Form;

use App\Entity\MaladiePoisson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MaladiePoissonForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'form-control';
        $selectClass = 'form-select';
        $textareaClass = 'form-control';

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'Ex: Points blancs',
                ],
            ])

            ->add('agentPathogene', TextType::class, [
                'label' => 'Agent pathogène',
                'required' => false,
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'Ex: Ichthyophthirius multifiliis',
                ],
            ])

            // Si tu veux un jour le passer en ChoiceType (faible/moyenne/élevée),
            // tu pourras, mais là on harmonise juste le rendu.
            ->add('gravite', TextType::class, [
                'label' => 'Gravité',
                'required' => false,
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'Ex: Faible / Moyenne / Élevée',
                ],
            ])

            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'required' => false,
                'choices' => [
                    'Bactérie' => 'Bactérie',
                    'Parasite' => 'Parasite',
                    'Virus' => 'Virus',
                    'Champignon' => 'Champignon',
                    'Autre' => 'Autre',
                ],
                'placeholder' => 'Non précisé',
                'attr' => [
                    'class' => $selectClass,
                ],
            ])

            ->add('contagieuse', CheckboxType::class, [
                'label' => 'Contagieuse',
                'required' => false,
                // look bootstrap-like
                'row_attr' => ['class' => 'form-check mb-3'],
                'label_attr' => ['class' => 'form-check-label'],
                'attr' => ['class' => 'form-check-input'],
            ])

            ->add('dureeTraitement', TextType::class, [
                'label' => 'Durée traitement',
                'required' => false,
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'Ex: 7 jours',
                ],
            ])

            ->add('symptomes', TextareaType::class, [
                'label' => 'Symptômes',
                'required' => false,
                'attr' => [
                    'class' => $textareaClass,
                    'rows' => 5,
                    'placeholder' => 'Décris les symptômes observables…',
                ],
            ])

            ->add('causes', TextareaType::class, [
                'label' => 'Causes',
                'required' => false,
                'attr' => [
                    'class' => $textareaClass,
                    'rows' => 5,
                    'placeholder' => 'Origines possibles / facteurs déclenchants…',
                ],
            ])

            ->add('traitement', TextareaType::class, [
                'label' => 'Traitement',
                'required' => false,
                'attr' => [
                    'class' => $textareaClass,
                    'rows' => 6,
                    'placeholder' => 'Protocole de traitement, produits, dosage…',
                ],
            ])

            ->add('prevention', TextareaType::class, [
                'label' => 'Prévention',
                'required' => false,
                'attr' => [
                    'class' => $textareaClass,
                    'rows' => 5,
                    'placeholder' => 'Mesures de prévention / bonnes pratiques…',
                ],
            ])

            ->add('imageFile', FileType::class, [
                'label' => 'Image (facultatif)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
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


