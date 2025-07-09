<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class EspeceSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('nom', SearchType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Rechercher par nom ou biotope...',
                'class' => 'form-control me-2',
            ],
        ]);
    }
}

