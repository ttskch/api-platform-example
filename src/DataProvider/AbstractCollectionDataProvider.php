<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @template T
 */
abstract class AbstractCollectionDataProvider
{
    /**
     * @var iterable<ContextAwareQueryCollectionExtensionInterface|ContextAwareQueryResultCollectionExtensionInterface>
     */
    protected iterable $extensions;

    /**
     * @param iterable<ContextAwareQueryCollectionExtensionInterface|ContextAwareQueryResultCollectionExtensionInterface> $collectionExtensions
     */
    #[Required]
    final public function setExtension(iterable $collectionExtensions): void
    {
        $this->extensions = $collectionExtensions;
    }

    /**
     * @return iterable<T>
     */
    final protected function getResult(QueryBuilder $qb, string $resourceClass, ?string $operationName, array $context): iterable
    {
        $resultExtension = null;

        foreach ($this->extensions as $extension) {
            $extension->applyToCollection($qb, $generator ??= new QueryNameGenerator(), $resourceClass, $operationName, $context);

            if ($extension instanceof ContextAwareQueryResultCollectionExtensionInterface) {
                $resultExtension = $extension;
            }
        }

        /** @var iterable<T> $result */
        $result = $resultExtension ? $resultExtension->getResult($qb, $resourceClass, $operationName, $context) : $qb->getQuery()->getResult();

        return $result;
    }
}
