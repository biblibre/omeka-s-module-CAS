<?php

namespace CAS\Service\Controller;

use CAS\Controller\SloController;
use CAS\Session\TicketStorage;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Log\PsrLoggerAdapter;

class SloControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $laminasLogger = $services->get('Omeka\Logger');

        return new SloController(
            $services->get(TicketStorage::class),
            new PsrLoggerAdapter($laminasLogger)
        );
    }
}