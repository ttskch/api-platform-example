<?php

declare(strict_types=1);

namespace App\DataProvider\Comment;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Comment;
use App\Repository\CommentRepository;

final class CollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private CommentRepository $commentRepository)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Comment::class === $resourceClass;
    }

    /**
     * @return iterable<Comment>
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /** @var iterable<Comment> $comments */
        $comments = $this->commentRepository->findBy(['isBanned' => false]);

        return $comments;
    }
}
