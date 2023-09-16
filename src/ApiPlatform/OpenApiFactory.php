<?php

declare(strict_types=1);

namespace App\ApiPlatform;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $paths = $openApi->getPaths()->getPaths();
        $fixedPaths = new Paths();

        /**
         * @var string   $path
         * @var PathItem $pathItem
         */
        foreach ($paths as $path => $pathItem) {
            $fixedPathItem = new PathItem();

            foreach (PathItem::$methods as $method) {
                $getter = sprintf('get%s', ucfirst(strtolower($method)));
                $setter = sprintf('with%s', ucfirst(strtolower($method)));

                $operation = $pathItem->$getter();
                assert($operation instanceof Operation || null === $operation);

                // hide operations which include "#hidden" in description from API Doc
                if ($operation && preg_match('/#hidden/', strval($operation->getDescription()))) {
                    continue;
                }

                // remove request body in operations witch include "#noRequestBody" in description from API Doc
                if ($operation && preg_match('/#noRequestBody/', strval($operation->getDescription()))) {
                    $description = strval(preg_replace('/\s*#noRequestBody\s*/', '', strval($operation->getDescription())));
                    $operation = $operation->withRequestBody()->withDescription($description);
                }

                if ($operation) {
                    $fixedPathItem = $fixedPathItem->$setter($operation);
                }
            }

            $fixedPaths->addPath($path, $fixedPathItem);
        }

        return $openApi->withPaths($fixedPaths);
    }
}
