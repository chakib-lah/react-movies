<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MoviePhotoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: MoviePhotoRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
        ],
    ],
    denormalizationContext: ['groups' => ['moviePhoto:write']],
    normalizationContext: ['groups' => ['moviePhoto:read']],
)]
class MoviePhoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: 'moviesPhotos')]
    #[Groups(['moviePhoto:write', 'moviePhoto:read'])]
    private $movies;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['moviePhoto:read'])]
    private $picture;

    #[Groups(['moviePhoto:read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'movies', fileNameProperty: 'picture')]
    #[Groups(['moviePhoto:write', 'moviePhoto:read'])]
    public ?File $pictureFile = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Movie|null
     */
    public function getMovies(): ?Movie
    {
        return $this->movies;
    }

    /**
     * @param Movie|null $movies
     * @return $this
     */
    public function setMovies(?Movie $movies): self
    {
        $this->movies = $movies;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     * @return $this
     */
    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
