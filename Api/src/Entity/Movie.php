<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\MovieRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: MovieRepository::class)]
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
    denormalizationContext: ['groups' => ['movie:write']],
    normalizationContext: ['groups' => ['movie:read']],
    paginationClientItemsPerPage: true
)]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['dateRelease' => 'DESC'])]
#[ApiFilter(DateFilter::class, properties: ['dateRelease'])]
#[ApiFilter(RangeFilter::class, properties: ['score'])]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['movie:write', 'movie:read'])]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['movie:write', 'movie:read'])]
    private $description;

    #[Groups(['movie:read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'movies', fileNameProperty: 'cover')]
    #[Groups(['movie:write'])]
    public ?File $coverFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['movie:read'])]
    private $cover;

    #[ORM\OneToMany(mappedBy: 'movies', targetEntity: MoviePhoto::class)]
    #[Groups(['movie:read'])]
    private $moviesPhotos;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['movie:write', 'movie:read'])]
    private $score;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['movie:write', 'movie:read'])]
    private $country;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['movie:write', 'movie:read'])]
    private $dateRelease;

    #[ORM\ManyToMany(targetEntity: Actor::class, mappedBy: "movies")]
    #[Groups(['movie:write', 'movie:read'])]
    private $actors;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: "movies")]
    #[Groups(['movie:write', 'movie:read'])]
    private $categories;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: "movies")]
    #[Groups(['movie:write', 'movie:read'])]
    private $authors;

    #[ORM\OneToMany(mappedBy: "movies", targetEntity: Comment::class, orphanRemoval: true)]
    #[Groups(['movie:read'])]
    private $comments;

    public function __construct()
    {
        $this->actors = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->moviesPhotos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Movie
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCover(): ?string
    {
        return $this->cover;
    }

    /**
     * @param string|null $cover
     * @return Movie
     */
    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return Collection|MoviePhoto[]
     */
    public function getMoviesPhotos(): Collection
    {
        return $this->moviesPhotos;
    }

    /**
     * @param MoviePhoto $moviePhoto
     * @return $this
     */
    public function addMoviePhoto(MoviePhoto $moviePhoto): self
    {
        if (!$this->moviesPhotos->contains($moviePhoto)) {
            $this->moviesPhotos[] = $moviePhoto;
            $moviePhoto->setMovies($this);
        }

        return $this;
    }

    /**
     * @param MoviePhoto $moviePhoto
     * @return $this
     */
    public function removeMoviePhoto(MoviePhoto $moviePhoto): self
    {
        if ($this->moviesPhotos->contains($moviePhoto)) {
            $this->moviesPhotos->removeElement($moviePhoto);
            // set the owning side to null (unless already changed)
            if ($moviePhoto->getMovies() === $this) {
                $moviePhoto->setMovies(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getScore(): ?string
    {
        return $this->score;
    }

    /**
     * @param string|null $score
     * @return Movie
     */
    public function setScore(?string $score): self
    {
        $this->score = $score;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return Movie
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateRelease(): DateTime
    {
        return $this->dateRelease;
    }

    /**
     * @param DateTime $dateRelease
     * @return Movie
     */
    public function setDateRelease(DateTime $dateRelease): self
    {
        $this->dateRelease = $dateRelease;

        return $this;
    }

    /**
     * @return Collection|Actor[]
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
        }
        return $this;
    }

    public function removeActor(Actor $actor): self
    {
        $this->actors->removeElement($actor);

        return $this;
    }

    /**
     * @return Collection|Actor[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $categories): self
    {
        if (!$this->categories->contains($categories)) {
            $this->categories[] = $categories;
        }
        return $this;
    }

    public function removeCategory(Category $categories): self
    {
        $this->categories->removeElement($categories);

        return $this;
    }

    /**
     * @return Author|null
     */
    public function getAuthors(): ?Author
    {
        return $this->authors;
    }

    /**
     * @param Author|null $authors
     * @return Movie
     */
    public function setAuthors(?Author $authors): self
    {
        $this->authors = $authors;
        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comments
     * @return $this
     */
    public function addComment(Comment $comments): self
    {
        if (!$this->comments->contains($comments)) {
            $this->comments[] = $comments;
            $comments->setMovies($this);
        }

        return $this;
    }

    public function removeComment(Comment $comments): self
    {
        if ($this->comments->contains($comments)) {
            $this->comments->removeElement($comments);
            if ($comments->getMovies() === $this) {
                $comments->setMovies(null);
            }
        }

        return $this;
    }


}
