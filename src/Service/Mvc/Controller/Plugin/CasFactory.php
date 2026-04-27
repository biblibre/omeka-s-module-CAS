<?php

namespace CAS\Service\Mvc\Controller\Plugin;

use CAS\Mvc\Controller\Plugin\Cas;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CasFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, ?array $options = null)
    {
        $casService = $services->get('CAS\CasService');

        $controllerPlugin = new Cas($casService);

        return $controllerPlugin;
    }
}
