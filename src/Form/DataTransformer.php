<?php namespace App\Form\DataTransformer;

use App\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RoleToLabelTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($roles)
{
    if (null === $roles) {
        return [];
    }

    return $roles;
}

public function reverseTransform($roleLabels)
{
    if (!$roleLabels) {
        return new ArrayCollection();
    }

    $roles = [];

    foreach ($roleLabels as $roleLabel) {
        
        
        $role = $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy(['label' => $roleLabel->getLabel()]);

        if (null === $role) {
            throw new TransformationFailedException(sprintf(
                'A role with label "%s" does not exist!',
                $roleLabel
            ));
        }

        $roles[] = $role;

    }

    return new ArrayCollection($roles);
}
}

?>