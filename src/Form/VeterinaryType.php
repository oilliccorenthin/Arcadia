<?php

namespace App\Form;

use App\Entity\RapportVeterinaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VeterinaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeFood', TextType::class, [
                'label' => false, 
                'constraints' => [
                    new Length(['min' => 3, 'max' => 50]),
                    new NotBlank(),
                ],
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

