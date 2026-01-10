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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
                'label' => 'Titre de l\'article'
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Biotope Amérique du sud'      => 'Biotope Amérique du sud',
                    'Biotope Amérique centrale'    => 'Biotope Amérique centrale',
                    'Biotope asiatique'            => 'Biotope asiatique',
                    'Biotope africain'             => 'Biotope africain',
                    'Biotope australien'           => 'Biotope australien',
                    'Biotope européen'             => 'Biotope européen',
                    'Biotope eaux saumâtres'       => 'Biotope eaux saumâtres',
                    'Biotope mangroves'            => 'Biotope mangroves',
                    'Autre'                        => 'Autre',
                ],
                'placeholder' => 'Choisir une catégorie',
                'required' => true,
            ])
            ->add('chapeau', TextareaType::class, [
                'required' => false,
                'label' => 'Chapeau (introduction)',
                'attr' => [
            'rows' => 4,
            'maxlength' => 300,        // ajuste si tu veux
            'class' => 'form-control autogrow',
            'placeholder' => 'Un court résumé accrocheur…',
            ],
                'help' => 'Court résumé affiché sous le titre de l\'article.',
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['rows' => 12]
            ])
            ->add('imageHeader', FileType::class, [
                'label' => 'Image d\'en-tête',
                'mapped' => false, 
                'required' => false,
            ])

            ->add('legendeImageHeader', TextType::class, [
                'label' => 'Légende de l\'image d\'en-tête',
                'required' => false,
            ])

            // Images intégrées dans le flux

            ->add('imageGauche', FileType::class, [ 
                'required' => false, 
                'mapped' => false, /* … */ 
                ])
            ->add('legendeImageGauche', null, [
                'required' => false
                ])
            ->add('imageDroite', FileType::class, [ 
                'required' => false, 
                'mapped' => false, /* … */ 
                ])
            ->add('legendeImageDroite', null, [
                'required' => false
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
            ->add('statut', ChoiceType::class, [
                'choices' => [
                'Brouillon' => 'brouillon',
                'Publié' => 'publie',
            ],
            'label' => 'Statut de publication',
            ])
            ->add('pullQuoteTexte', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'maxlength' => 1000,         // cohérent avec Assert\Length(max=1000)
                    'placeholder' => '« Votre citation… »'
                ],
                'label' => 'Pull-quote — Texte',
            ])

            ->add('pullQuoteSource', TextType::class, [
                'required' => false,
                'attr' => [
                    'maxlength' => 255,          // cohérent avec Assert\Length(max=255)
                    'placeholder' => 'Auteur / Source (optionnel)'
                ],
                'label' => 'Pull-quote — Source',
            ])
            ->add('pullQuotePosition', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Droite (défaut)',
                'choices' => ['Gauche'=>'left','Droite'=>'right','Centré'=>'center'],
                'attr' => ['data-pq-target' => 'position', 'data-action' => 'change->pq#update'],
            ])
            ->add('pullQuoteTheme', ChoiceType::class, [
                'required' => false,
                'choices' => ['Par défaut'=>'default','Vert'=>'green','Orange'=>'orange','Violet'=>'purple'],
                'attr' => ['data-pq-target' => 'theme', 'data-action' => 'change->pq#update'],
            ])
            ->add('pullQuoteIndex', IntegerType::class, [
                'required' => false,
                'empty_data' => '2',
                'attr' => ['min' => 1, 'max' => 50], // borne haute “safe”
                'label' => 'Pull-quote — Insérer après le paragraphe n°',
            ]);

        // Afficher le champ user uniquement pour l’administrateur
        
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email', // ou 'pseudo'
                'label' => 'Auteur de l\'article',
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



