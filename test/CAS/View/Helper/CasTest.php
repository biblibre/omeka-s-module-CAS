<?php

namespace CAS\Test\View\Helper;

use CAS\Test\TestCase;
use CAS\Entity\CasUser;
use CAS\CasService;

class CasTest extends TestCase
{
    public function testInvoke(): void
    {
        $services = $this->getServiceLocator();
        $viewHelperManager = $services->get('ViewHelperManager');
        $cas = $viewHelperManager->get('cas');

        $this->assertInstanceOf(CasService::class, $cas());
    }
}
