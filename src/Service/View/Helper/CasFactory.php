<?php

namespace CAS\Service\View\Helper;

use CAS\View\Helper\Cas;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CasFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, ?array $options = null)
    {
        $casService = $services->get('CAS\CasService');

        $viewHelper = new Cas($casService);

        return $viewHelper;
    }
}
