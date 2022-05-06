<?php

declare(strict_types=1);

namespace App\ApiPlatform;

use ApiPlatform\Core\DataProvider\OperationDataProviderTrait;
use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\EventListener\ReadListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onPreRead', EventPriorities::PRE_READ],
        ];
    }

    /**
     * @see ReadListener::onKernelRequest()
     * @see OperationDataProviderTrait::extractIdentifiers()
     */
    public function onPreRead(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $parameters = $request->attributes;

        // @see https://github.com/api-platform/api-platform/issues/702#issuecomment-474889155
        if (!$parameters->get('id')) {
            $parameters->set('id', 0);
        }
    }
}
