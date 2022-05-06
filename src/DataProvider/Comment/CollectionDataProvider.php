<?php

declare(strict_types=1);

namespace App\DataProvider\Comment;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\DataProvider\AbstractCollectionDataProvider;
use App\Entity\Comment;
use App\Repository\CommentRepository;

/**
 * @extends AbstractCollectionDataProvider<Comment>
 */
final class CollectionDataProvider extends AbstractCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
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
        $qb = $this->commentRepository->createQueryBuilder('co')
            ->andWhere('co.isBanned = false')
        ;

        return $this->getResult($qb, $resourceClass, $operationName, $context);
    }
}
