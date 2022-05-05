<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Comment\CollectionPostController;
use App\Controller\Comment\ItemBanController;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'path' => '/posts/{id}/comments',
            'controller' => CollectionPostController::class,
        ],
    ],
    itemOperations: [
        'get',
        'put',
        'patch',
        'delete',
        'ban' => [
            'method' => 'put',
            'path' => '/comments/{id}/ban',
            'controller' => ItemBanController::class,
            'input' => false,
        ],
    ],
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Post $post = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $body = '';

    #[ORM\Column(type: 'boolean')]
    private bool $isBanned = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getIsBanned(): bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }
}
