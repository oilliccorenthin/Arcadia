<?php

namespace App\Form;

use App\Entity\Habitat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class HabitatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'constraints' => [
                new Length(['min' => 3, 'max' => 50]),
                new NotBlank(),
            ],
        ])
        ->add('description', TextType::class, [
            'constraints' => [
                new Length(['min' => 3, 'max' => 50]),
                new NotBlank(),
            ],
        ])
        ->add('images', CollectionType::class, [
            'entry_type' => ImageType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'entry_options' => ['em' => $options['em']],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Habitat::class,
        ]);
        $resolver->setRequired('em');
        $resolver->setAllowedTypes('em', EntityManagerInterface::class);
    }
}
