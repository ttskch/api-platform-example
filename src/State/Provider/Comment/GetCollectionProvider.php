<?php

declare(strict_types=1);

namespace App\State\Provider\Comment;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Traits\CollectionStateProviderTrait;

/**
 * @implements ProviderInterface<Comment>
 */
class GetCollectionProvider implements ProviderInterface
{
    /**
     * @phpstan-use CollectionStateProviderTrait<Comment>
     */
    use CollectionStateProviderTrait;

    public function __construct(private CommentRepository $commentRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $qb = $this->commentRepository->createQueryBuilder('co')
            ->leftJoin('co.post', 'po')
            ->andWhere('po.id = :postId')
            ->andWhere('co.isBanned = false')
            ->setParameter('postId', $uriVariables['postId'])
        ;

        return $this->getResult($qb, Comment::class, $operation, $context);
    }
}
