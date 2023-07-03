<?php declare(strict_types=1);

namespace CAS\Service\ControllerPlugin;

use CAS\Mvc\Controller\Plugin\IsCasUser;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IsCasUserFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new IsCasUser($services->get('Omeka\EntityManager'));
    }
}
