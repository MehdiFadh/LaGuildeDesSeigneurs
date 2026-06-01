<?php

// src/Form/CharacterType.php
// Formulaire Symfony (FormType) utilisé pour l'hydratation, la validation et le traitement des données envoyées (payload) pour les personnages.

namespace App\Form;

use App\Entity\Character;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('utilisateur')
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('caste', TextType::class)
            ->add('knowledge', TextType::class)
            ->add('intelligence', IntegerType::class)
            ->add('strength', IntegerType::class)
            ->add('image', TextType::class)
            ->add('slug', TextType::class)
            ->add('kind', TextType::class)
            ->add('creation', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('identifier', TextType::class)
            ->add('modification', DateTimeType::class, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Character::class,
        ]);
    }
}
