<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Imagine\Gd\Imagine;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: "imageName")]
    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png"]
    )]
    private ?File $imageFile = null;

    #[ORM\Column(type: "string")]
    private ?string $imageName = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Habitat $habitat = null;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt = null;


    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            // It's very important to add this condition, because VichUploader may try to set imageFile with null value when you update your entity
            if ($imageFile instanceof UploadedFile) {
                $this->updatedAt = new \DateTime('now');
            }
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PreRemove]
    public function preRemove(): void
    {
        // Remove the file from the filesystem
        $file = $this->getImageFile();
        if ($file && file_exists($file->getPathname())) {
            unlink($file->getPathname());
        }
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        // If name is null, remove the image
        if (null === $this->imageName) {
            $this->habitat->removeImage($this); 
        }
    }

    public function resizeImage(ImageInterface $image): void
    {
        // Calculate aspect ratio
        $originalWidth = $image->getSize()->getWidth();
        $originalHeight = $image->getSize()->getHeight();
        $ratio = $originalWidth / $originalHeight;

        // Calculate new dimensions
        $newWidth = 800;
        $newHeight = 450;

        // Resize image
        $image->resize(new Box($newWidth, $newHeight));

        // Crop image to maintain aspect ratio
        $cropWidth = $originalWidth;
        $cropHeight = $originalHeight;
        if ($ratio > 800 / 450) {
            $cropWidth = $originalHeight * (800 / 450);
        } else {
            $cropHeight = $originalWidth * (450 / 800);
        }
        $image->crop(new Point(($originalWidth - $cropWidth) / 2, ($originalHeight - $cropHeight) / 2), new Box($cropWidth, $cropHeight));
    }
}
