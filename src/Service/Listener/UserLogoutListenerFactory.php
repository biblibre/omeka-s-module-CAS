<?php

namespace CAS\Service\Listener;

use CAS\Listener\UserLogoutListener;
use CAS\Session\TicketStorage;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserLogoutListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new UserLogoutListener(
            $services->get('Omeka\Settings'),
            $services->get(TicketStorage::class),
            $services
        );
    }
}

