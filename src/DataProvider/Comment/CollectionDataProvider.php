<?php

declare(strict_types=1);

namespace App\DataProvider\Comment;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\DataProvider\AbstractCollectionDataProvider;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        if (!preg_match('#^/api/v1/posts/(\d+)/comments#', strval($context['request_uri']), $m)) {
            throw new NotFoundHttpException();
        }

        $postId = intval($m[1]);

        $qb = $this->commentRepository->createQueryBuilder('co')
            ->leftJoin('co.post', 'po')
            ->andWhere('po.id = :postId')
            ->andWhere('co.isBanned = false')
            ->setParameter('postId', $postId)
        ;

        return $this->getResult($qb, $resourceClass, $operationName, $context);
    }
}
