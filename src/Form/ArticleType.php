<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l’article'
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['rows' => 10]
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Biotope asiatique' => 'Biotope asiatique',
                    'Biotope Amérique du sud' => 'Biotope Amérique du sud',
                    'Biotope africain' => 'Biotope africain',
                    'Matériel' => 'Matériel',
                    'Maintenance' => 'Maintenance',
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Illustration',
                'mapped' => false,
                'required' => false
            ])
            ->add('datePublication', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('statut');

        // Afficher le champ user uniquement pour l’administrateur
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email', // ou 'pseudo' si tu préfères
                'label' => 'Auteur de l’article',
                'required' => false,
                'placeholder' => 'Sélectionnez un auteur',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}



