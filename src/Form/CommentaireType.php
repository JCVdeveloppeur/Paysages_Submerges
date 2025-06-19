<?php

namespace App\Form;

use App\Entity\Commentaire;
use App\Entity\Article;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isAuthenticated = $options['is_authenticated'] ?? false;

        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => [
                    'rows' => $isAuthenticated ? 6 : 3,
                    'maxlength' => $isAuthenticated ? 1000 : 300,
                    'placeholder' => $isAuthenticated 
                        ? 'Exprimez-vous en toute liberté (jusqu’à 1000 caractères)...' 
                        : 'Laissez un petit mot (300 caractères max)',
                ],
            ]);

        // 🔒 Champs internes, visibles seulement si connecté (admin ou utilisateur)
        if ($isAuthenticated) {
            $builder
                ->add('dateCommentaire', DateTimeType::class, [
                    'widget' => 'single_text',
                    'label' => 'Date du commentaire',
                ])
                ->add('approuve', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Approuvé ?',
                ])
                ->add('auteur', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'email',
                    'label' => 'Auteur du commentaire',
                    'disabled' => true, // ⛔ empêcher modification
                ])

                ->add('article', EntityType::class, [
                    'class' => Article::class,
                    'choice_label' => 'titre',
                    'label' => 'Article concerné',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
            'is_authenticated' => false,
        ]);
    }
}

