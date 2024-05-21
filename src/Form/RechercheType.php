<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RechercheType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search_term', TextType::class, [
                'label' => 'Terme de recherche',
                'attr' => [
                    'placeholder' => 'Entrez votre terme de recherche',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
            ]);
    }
}
