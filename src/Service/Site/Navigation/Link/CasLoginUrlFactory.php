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
        $request = $container->get('Request');

        $casLoginUrl = new CasLoginUrl($authenticationService, $request);

        return $casLoginUrl;
    }
}
