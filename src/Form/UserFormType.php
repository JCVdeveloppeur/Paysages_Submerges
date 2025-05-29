<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => ['placeholder' => 'exemple@aquarium.fr']
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'attr' => ['placeholder' => 'Choisissez un pseudo stylé 🕶️'],
            ])
            ->add('username', TextType::class, [
                'label' => 'Nom d’utilisateur',
                'attr' => ['placeholder' => 'Nom public ou identifiant'],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false, // Permet de ne pas le changer en édition
                'attr' => ['placeholder' => 'Nouveau mot de passe si besoin']
            ])
            ->add('bio', TextType::class, [
                'label' => 'Biographie',
                'required' => false,
                'attr' => ['placeholder' => 'Quelques mots sur vous...']
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('dateInscription', DateTimeType::class, [
                'label' => 'Date d\'inscription',
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}


