<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RoleToLabelTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password', TextType::class, [
                'constraints' => [
                    new Length(['min' => 3, 'max' => 50]),
                ],
            ])
            ->add(
                $builder->create('roleObjects', EntityType::class, [
                    'class' => Role::class,
                    'choice_label' => 'label', 
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false,
                ])->addModelTransformer(new RoleToLabelTransformer($this->entityManager))
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
