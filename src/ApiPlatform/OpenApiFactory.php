<?php

declare(strict_types=1);

namespace App\ApiPlatform;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        /**
         * @var string   $path
         * @var PathItem $pathItem
         */
        foreach ($openApi->getPaths()->getPaths() as $path => $pathItem) {
            // hide operations which include "#hidden" in description from API Doc
            if ($pathItem->getGet() && preg_match('/#hidden/', $pathItem->getGet()->getDescription())) {
                $openApi->getPaths()->addPath($path, $pathItem->withGet(null));
            }

            // remove request body in operations witch include "#withoutRequestBody" in description from API Doc
            foreach (PathItem::$methods as $method) {
                $getter = 'get'.ucfirst(strtolower($method));
                $setter = 'with'.ucfirst(strtolower($method));
                /** @var Operation|null $operation */
                $operation = $pathItem->$getter();
                if ($operation && preg_match('/#withoutRequestBody/', $operation->getDescription())) {
                    // use reflection because $operation->requestBody cannot be reset to null except in the constructor
                    $reflectionProperty = new \ReflectionProperty($operation, 'requestBody');
                    $reflectionProperty->setAccessible(true);
                    $reflectionProperty->setValue($operation, null);
                    /** @var Parameter[] $parameters */
                    $description = trim(strval(preg_replace('/#withoutRequestBody/', '', $operation->getDescription())));
                    $openApi->getPaths()->addPath($path, $pathItem = $pathItem->$setter($operation->withDescription($description)));
                }
            }

            // remove links in operations which include "#withoutLinks" in description from API Doc
            foreach (PathItem::$methods as $method) {
                $getter = 'get'.ucfirst(strtolower($method));
                $setter = 'with'.ucfirst(strtolower($method));
                /** @var Operation|null $operation */
                $operation = $pathItem->$getter();
                if ($operation && preg_match('/#withoutLinks/', $operation->getDescription())) {
                    $responses = [];
                    /** @var Response $response */
                    foreach ($operation->getResponses() as $statusCode => $response) {
                        // use reflection because $operation->requestBody cannot be reset to null except in the constructor
                        $reflectionProperty = new \ReflectionProperty($response, 'links');
                        $reflectionProperty->setAccessible(true);
                        $reflectionProperty->setValue($response, null);
                        $responses[$statusCode] = $response;
                    }
                    $description = trim(strval(preg_replace('/#withoutLinks/', '', $operation->getDescription())));
                    $openApi->getPaths()->addPath($path, $pathItem = $pathItem->$setter($operation->withDescription($description)->withResponses($responses)));
                }
            }
        }

        return $openApi;
    }
}
