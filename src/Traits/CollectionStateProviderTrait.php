<?php

declare(strict_types=1);

namespace App\Traits;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @template T
 */
trait CollectionStateProviderTrait
{
    /**
     * @var iterable<QueryCollectionExtensionInterface|QueryResultCollectionExtensionInterface>
     */
    protected iterable $extensions;

    /**
     * @param iterable<QueryCollectionExtensionInterface|QueryResultCollectionExtensionInterface> $collectionExtensions
     */
    #[Required]
    public function setExtension(iterable $collectionExtensions): void
    {
        $this->extensions = $collectionExtensions;
    }

    /**
     * @return iterable<T>
     */
    protected function getResult(QueryBuilder $qb, string $resourceClass, Operation $operation, array $context): iterable
    {
        $resultExtension = null;

        foreach ($this->extensions as $extension) {
            $extension->applyToCollection($qb, new QueryNameGenerator(), $resourceClass, $operation, $context);

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operation, $context)) {
                $resultExtension ??= $extension;
            }
        }

        /** @var iterable<T> $result */
        $result = $resultExtension ? $resultExtension->getResult($qb, $resourceClass, $operation, $context) : $qb->getQuery()->getResult();

        return $result;
    }
}
