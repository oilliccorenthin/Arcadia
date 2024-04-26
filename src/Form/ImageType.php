<?php
namespace App\Form;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Image\ImagineInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Selectionnez puis valider l\'édition pour supprimer',
                'download_uri' => true,
                'image_uri' => true,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $image = $event->getData();
                $form = $event->getForm();
                if ($form->get('imageFile')->getData() === null) {
                    $entityManager = $form->getConfig()->getOption('em');
                    $entityManager->remove($image);
                    $entityManager->flush();
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
        $resolver->setRequired('em');
        $resolver->setAllowedTypes('em', EntityManagerInterface::class);
    }
}
?>