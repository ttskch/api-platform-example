<?php

declare(strict_types=1);

namespace App\DataProvider\Comment;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\QueryBuilder;

final class CollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private iterable $collectionExtensions, private CommentRepository $commentRepository)
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

        /** @var iterable<Comment> $comments */
        $result = $this->getResult($qb, $resourceClass, $operationName, $context);

        return $result;
    }

    private function getResult(QueryBuilder $qb, string $resourceClass, ?string $operationName, array $context): iterable
    {
        $resultExtension = null;

        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($qb, $generator ??= new QueryNameGenerator(), $resourceClass, $operationName, $context);

            if ($extension instanceof ContextAwareQueryResultCollectionExtensionInterface) {
                $resultExtension = $extension;
            }
        }

        /** @var iterable $result */
        $result = $resultExtension ? $resultExtension->getResult($qb, $resourceClass, $operationName, $context) : $qb->getQuery()->getResult();

        return $result;
    }
}
