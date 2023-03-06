<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ActorRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: ActorRepository::class)]
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
    denormalizationContext: ['groups' => ['actor:write']],
    normalizationContext: ['groups' => ['actor:read']],
)]
class Actor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 60)]
    #[Groups(['actor:write', 'actor:read', 'movie:read'])]
    private $firstName;

    #[ORM\Column(type: 'string', length: 60)]
    #[Groups(['actor:write', 'actor:read', 'movie:read'])]
    private $lastName;

    #[ORM\Column(type: 'date')]
    #[Groups(['actor:write', 'actor:read'])]
    private $birthDate;

    #[Groups(['actor:read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'actors', fileNameProperty: 'picture')]
    #[Groups(['actor:write'])]
    public ?File $pictureFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['actor:read'])]
    private $picture;

    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: "actors")]
    #[ORM\JoinTable(name: "actors_movies")]
    #[Groups(['actor:read'])]
    private $movies;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBirthDate(): DateTime
    {
        return $this->birthDate;
    }

    /**
     * @param DateTime $birthDate
     * @return Actor
     */
    public function setBirthDate(DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;
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
     * @param string|null $picture
     * @return Actor
     */
    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;
        return $this;
    }


    /**
     * @return Collection|Movie[]
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
            $movie->addActor($this);
        }
        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->removeElement($movie)) {
            $movie->removeActor($this);
        }
        return $this;
    }


}
