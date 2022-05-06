<?php

declare(strict_types=1);

namespace App\ApiPlatform;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Api\UrlGeneratorInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * @see \ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter
 */
class IriConverter implements IriConverterInterface
{
    public function __construct(
        private \ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter $decorated,
        private RouteNameResolverInterface $routeNameResolver,
        private RouterInterface $router,
        private RequestStack $requestStack,
    ) {
    }

    public function getItemFromIri(string $iri, array $context = []): object
    {
        return $this->decorated->getItemFromIri($iri, $context);
    }

    public function getIriFromItem($item, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        return $this->decorated->getIriFromItem($item, $referenceType);
    }

    public function getIriFromResourceClass(string $resourceClass, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        // customize this process to enable to declare Collection Get operations with URI parameters
        $iri = $this->getIriWithRouteParametersFromResourceClass($resourceClass, $referenceType);

        return $iri ?? $this->decorated->getIriFromResourceClass($resourceClass, $referenceType);
    }

    public function getItemIriFromResourceClass(string $resourceClass, array $identifiers, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        return $this->decorated->getItemIriFromResourceClass($resourceClass, $identifiers, $referenceType);
    }

    public function getSubresourceIriFromResourceClass(string $resourceClass, array $identifiers, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        return $this->decorated->getSubresourceIriFromResourceClass($resourceClass, $identifiers, $referenceType);
    }

    private function getIriWithRouteParametersFromResourceClass(string $resourceClass, int $referenceType = UrlGeneratorInterface::ABS_PATH): string|null
    {
        if (Comment::class === $resourceClass) {
            $requestUri = $this->requestStack->getCurrentRequest()?->getRequestUri() ?? '';
            preg_match('#^/api/v1/posts/(\d+)/comments#', $requestUri, $m);
            $postId = intval($m[1]);

            return $this->router->generate($this->routeNameResolver->getRouteName($resourceClass, OperationType::COLLECTION), [
                'id' => $postId,
            ], $referenceType);
        }

        return null;
    }
}
