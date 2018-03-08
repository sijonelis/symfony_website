<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;

class ApiVersionListener
{

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'))->get('application/yipiao.api+json');

        if (!is_null($acceptHeader)) {
            $version = $acceptHeader->getAttribute('version');
            $request->attributes->set('version', $version);
        }

    }
}