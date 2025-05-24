<?php

namespace App\Form;

use App\Entity\Commentaire;
use App\Entity\Article;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => ['rows' => 5],
            ])
            ->add('dateCommentaire')
            ->add('approuve', CheckboxType::class, [
                'required' => false,
                'label' => 'Approuvé ?',
            ])
            ->add('auteur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email', // à adapter selon ce que tu veux afficher
                'label' => 'Auteur du commentaire',
            ])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'titre',
                'label' => 'Article associé',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
