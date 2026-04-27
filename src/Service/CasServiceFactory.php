<?php

namespace CAS\Service;

use CAS\CasService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CasServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, ?array $options = null)
    {
        $authenticationService = $services->get('Omeka\AuthenticationService');
        $entityManager = $services->get('Omeka\EntityManager');
        $viewHelperManager = $services->get('ViewHelperManager');

        $helper = new CasService($authenticationService, $entityManager, $viewHelperManager);

        return $helper;
    }
}
