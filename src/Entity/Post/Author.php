<?php

declare(strict_types=1);

namespace App\Entity\Post;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Author
{
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[ApiProperty(attributes: [
        'openapi_context' => [
            'description' => '投稿者の名前',
        ],
    ])]
    private string $name = '';

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    #[ApiProperty(attributes: [
        'openapi_context' => [
            'description' => '投稿者の生年月日',
        ],
    ])]
    private ?\DateTimeInterface $birthDate = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }
}
