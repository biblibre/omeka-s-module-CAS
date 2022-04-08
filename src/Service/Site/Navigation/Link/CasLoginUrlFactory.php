<?php
namespace CAS\Service\Site\Navigation\Link;

use CAS\Site\Navigation\Link\CasLoginUrl;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class CasLoginUrlFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authenticationService = $container->get('Omeka\AuthenticationService');

        $casLoginUrl = new CasLoginUrl($authenticationService);

        return $casLoginUrl;
    }
}
