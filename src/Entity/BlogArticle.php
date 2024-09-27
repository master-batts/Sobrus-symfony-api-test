<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\BlogArticleRepository;
use App\State\Processors\BlogArticle\CreateBlogArticleProcessor;
use App\State\Processors\BlogArticle\PublishBlogArticleProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

#[ORM\Entity(repositoryClass: BlogArticleRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
#[ApiResource()]
#[Vich\Uploadable]
#[HasLifecycleCallbacks]
#[Post(
    processor: CreateBlogArticleProcessor::class,
    denormalizationContext: ['groups'=> ['write-denormalization:blog-article']],
    normalizationContext: ['groups'=> ['write-normalization:blog-article']],
    name: 'BlogArticleCreating',
    uriTemplate: 'blog-articles',
    inputFormats: ['multipart' => ['multipart/form-data']]
)]

#[Get(
    normalizationContext: ['groups'=> ['read:blog-article']],
    uriTemplate: 'blog-articles/{id}',
)]
#[GetCollection(
    normalizationContext: ['groups'=> ['read-collection:blog-article']],
    uriTemplate: 'blog-articles',
)]
#[Patch(
    denormalizationContext: ['groups'=> ['update:blog-article']],
    security: "object.getAuthorId() == user",
    uriTemplate: 'blog-articles/{id}'
)]
#[Patch(
    processor:PublishBlogArticleProcessor::class,
    normalizationContext: ['groups'=> ['publish:blog-article']],
    denormalizationContext: ['groups'=> ['denormalization-publish:blog-article']],
    uriTemplate: 'blog-article-publish/{id}',
    name: 'BlogArticlePublishing',
    security: "object.getAuthorId() == user"
)]
#[Delete(
    security: "object.getAuthorId() == user",
    uriTemplate: 'blog-articles/{id}'
)]
class BlogArticle
{
    use SoftDeleteableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['write-normalization:blog-article','publish:blog-article','read:blog-article','read-collection:blog-article'])]
    private ?int $id = null;

    #[Groups(['read:blog-article','read-collection:blog-article'])]
    #[ORM\ManyToOne(inversedBy: 'blogArticles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $authorId = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 4,
        max: 200,
        minMessage: 'Title must be at least {{ limit }} characters long',
        maxMessage: 'Title cannot be longer than {{ limit }} characters',
    )]
    #[Groups(['write-denormalization:blog-article','write-normalization:blog-article','update:blog-article','publish:blog-article','read:blog-article','read-collection:blog-article'])]
    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[Groups(['write-denormalization:blog-article','write-normalization:blog-article','update:blog-article','read:blog-article','read-collection:blog-article'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE,nullable: true)]
    private ?\DateTimeInterface $publicationDate = null;

    #[Groups(['read:blog-article','read-collection:blog-article'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[Groups(['write-denormalization:blog-article','write-normalization:blog-article','update:blog-article','read:blog-article','read-collection:blog-article'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[Groups(['write-normalization:blog-article','read:blog-article','read-collection:blog-article'])]
    #[ORM\Column]
    private array $keywords = [];

    #[Groups(['write-normalization:blog-article','update:blog-article','publish:blog-article','read:blog-article','read-collection:blog-article'])]
    #[Assert\Choice(["draft", "published", "deleted"])]
    #[ORM\Column(length: 100)]
    private ?string $status = null;

    #[Groups(['write-normalization:blog-article','write:blog-article','read:blog-article','read-collection:blog-article'])]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[Assert\File(
        maxSize: '10240k',
        extensions: ['jpeg','jpg','png'],
        extensionsMessage: 'Please upload a valid image',
    )]
    #[Groups(['write-denormalization:blog-article'])]
    #[Vich\UploadableField(mapping: 'blogArticles', fileNameProperty: 'coverPictureRef', size: 'coverPictureRefSize')]
    private ?File $coverPictureRefFile = null;
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['write-normalization:blog-article','read:blog-article','read-collection:blog-article'])]
    private ?string $coverPictureRef = null;

    #[ORM\Column(nullable: true)]
    private ?int $coverPictureRefSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAuthorId(): ?User
    {
        return $this->authorId;
    }

    public function setAuthorId(?User $authorId): static
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): static
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function setKeywords(array $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function setCoverPictureRefFile(?File $CoverPictureRefFile = null): void
    {
        $this->coverPictureRefFile = $CoverPictureRefFile;

        if (null !== $CoverPictureRefFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getCoverPictureRefFile(): ?File
    {
        return $this->coverPictureRefFile;
    }

    public function getCoverPictureRef(): ?string
    {
        return $this->coverPictureRef;
    }

    public function setCoverPictureRef(?string $coverPictureRef): static
    {
        $this->coverPictureRef = $coverPictureRef;

        return $this;
    }
    public function getCoverPictureRefSize(): ?int
    {
        return $this->coverPictureRefSize;
    }

    public function setCoverPictureRefSize(?int $coverPictureRefSize): self
    {
        $this->coverPictureRefSize = $coverPictureRefSize;

        return $this;
    }
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]

    public function slugify()
    {
        // Replace non letter or digits by -
        $slug = preg_replace('~[^\pL\d]+~u', '-', $this->title);

        // Transliterate to ASCII
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);

        // Remove unwanted characters
        $slug = preg_replace('~[^-\w]+~', '', $slug);

        // Trim
        $slug = trim($slug, '-');

        // Remove duplicate -
        $slug = preg_replace('~-+~', '-', $slug);

        // Lowercase
        $slug = strtolower($slug);

        $this->setSlug($slug);
    }
    #[ORM\PrePersist]
    public function updateDate()
    {
        if ($this->getCreationDate() == null) {
            $this->setCreationDate(new \DateTimeImmutable());
        }
    }
    //Part 2: Algorithmic Challenge//
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function extractTopKeywords(): void
    {
        $banned = ['the', 'is', 'in', 'and', 'to', 'a'];
        $text = $this->content;

        // Normalize text: lower case and remove punctuation
        $text = strtolower($text);
        $text = preg_replace('/[^\w\s]/', '', $text);

        // Split text into words
        $words = preg_split('/\s+/', $text);
        $wordCounts = [];

        // Count the occurrences of each word
        foreach ($words as $word) {
            if (!in_array($word, $banned) && !empty($word)) {
                if (!isset($wordCounts[$word])) {
                    $wordCounts[$word] = 0;
                }
                $wordCounts[$word]++;
            }
        }

        // Sort the words by frequency
        arsort($wordCounts);

        // Get the top 3 words
        $topWords = array_slice(array_keys($wordCounts), 0, 3);
        $this->setKeywords($topWords);
    }

}
