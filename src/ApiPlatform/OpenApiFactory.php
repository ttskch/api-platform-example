<?php

declare(strict_types=1);

namespace App\ApiPlatform;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\PathItem;
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
        }

        return $openApi;
    }
}
