<?php

declare(strict_types=1);

namespace App\State\Provider\Comment;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Comment;
use App\Repository\PostRepository;

/**
 * @implements ProviderInterface<Comment>
 */
class PostProvider implements ProviderInterface
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $post = $this->postRepository->find($uriVariables['postId']);

        return (new Comment())->setPost($post);
    }
}
