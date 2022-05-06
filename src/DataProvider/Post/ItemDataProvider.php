<?php

declare(strict_types=1);

namespace App\DataProvider\Post;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Post;
use App\Repository\PostRepository;

final class ItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Post::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Post
    {
        if ('latest' === $operationName || 'putLatest' === $operationName) {
            /** @var Post|null $post */
            $post = $this->postRepository->findOneBy([], ['id' => 'DESC']);

            return $post;
        }

        /** @var Post|null $post */
        $post = $this->postRepository->find(strval($id));

        return $post;
    }
}
