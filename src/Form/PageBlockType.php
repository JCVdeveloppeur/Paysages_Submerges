<?php

namespace App\Form;

use App\Entity\PageBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slug', TextType::class, [
                'label' => 'Identifiant unique (slug)',
                'help' => 'Exemple : accueil-apropos (ne pas utiliser d’espaces)',
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre (facultatif)',
                'required' => false,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'rows' => 10,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageBlock::class,
        ]);
    }
}

