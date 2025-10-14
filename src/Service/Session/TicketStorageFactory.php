<?php

namespace CAS\Service\Session;

use CAS\Session\TicketStorage;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TicketStorageFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new TicketStorage(
            $services->get('Omeka\EntityManager')
        );
    }
}
