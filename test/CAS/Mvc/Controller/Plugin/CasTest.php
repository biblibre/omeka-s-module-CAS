<?php

namespace CAS\Test\Mvc\Controller\Plugin;

use CAS\Test\TestCase;
use CAS\Entity\CasUser;
use CAS\CasService;

class CasTest extends TestCase
{
    public function testInvoke(): void
    {
        $services = $this->getServiceLocator();
        $controllerPluginManager = $services->get('ControllerPluginManager');
        $cas = $controllerPluginManager->get('cas');

        $this->assertInstanceOf(CasService::class, $cas());
    }
}
