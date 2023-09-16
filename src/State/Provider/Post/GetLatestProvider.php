<?php

declare(strict_types=1);

namespace App\State\Provider\Post;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Post;
use App\Repository\PostRepository;

/**
 * @implements ProviderInterface<Post>
 */
class GetLatestProvider implements ProviderInterface
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->postRepository->findOneBy([], ['id' => 'DESC']);
    }
}
