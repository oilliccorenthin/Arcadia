<?php

namespace App\Form;

use App\Entity\RapportVeterinaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VeterinaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeFood', TextType::class, [
                'label' => false, 
            ])
            ->add('gramFood', IntegerType::class, [
                'label' => false, 
            ])
            ->add('detail', TextType::class, [
                'label' => false, 
            ])
            ->add('state', TextType::class, [
                'label' => false, 
                'mapped' => false,
                'data' => $options['animal_state'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RapportVeterinaire::class,
            'animal_state' => null
        ]);
    }
}

