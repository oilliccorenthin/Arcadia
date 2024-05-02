<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Form\DataTransformer\RolesToStringTransformer;
use App\Form\DataTransformer\RoleToLabelTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                    new UniqueEntity([
                        'fields' => 'email',
                        'entityClass' => 'App\Entity\User',
                        'message' => 'Cette adresse email est déjà utilisée.',
                    ]),
                ],
            ])
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
