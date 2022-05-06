<?php

declare(strict_types=1);

namespace App\ApiPlatform;

use ApiPlatform\Core\Operation\PathSegmentNameGeneratorInterface;
use ApiPlatform\Core\Util\Inflector;

class LowerCamelCasePathSegmentNameGenerator implements PathSegmentNameGeneratorInterface
{
    public function getSegmentName(string $name, bool $collection = true): string
    {
        $name = lcfirst($name);

        return $collection ? Inflector::pluralize($name) : $name;
    }
}
