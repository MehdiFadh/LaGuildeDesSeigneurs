<?php

// src/Form/BuildingType.php
// Formulaire Symfony (FormType) utilisé pour l'hydratation, la validation et le traitement des données envoyées (payload) pour les bâtiments.

namespace App\Form;

use App\Entity\Building;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('slug', TextType::class)
            ->add('caste', TextType::class)
            ->add('strength', IntegerType::class)
            ->add('image', TextType::class)
            ->add('price', IntegerType::class)
            ->add('stars', IntegerType::class)
            ->add('identifier', TextType::class)
            ->add('creation', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('modification', DateTimeType::class, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Building::class,
        ]);
    }
}
