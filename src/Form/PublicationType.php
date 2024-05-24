<?php

namespace App\Form;

use App\Entity\Publication;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true,
                'attr' => [
                    'rows' => 5,
                    'class' => 'form-control',
                ],
            ])
            ->add('media', FileType::class, [
                'label' => 'Media (Image)',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
