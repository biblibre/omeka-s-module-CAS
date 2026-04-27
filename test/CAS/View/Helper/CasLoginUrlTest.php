<?php

namespace CAS\Test\View\Helper;

use CAS\Test\TestCase;
use CAS\Entity\CasUser;
use CAS\CasService;

class CasLoginUrlTest extends TestCase
{
    public function testInvoke(): void
    {
        $services = $this->getServiceLocator();
        $viewHelperManager = $services->get('ViewHelperManager');
        $casLoginUrl = $viewHelperManager->get('casLoginUrl');

        $url = $casLoginUrl();
        $this->assertEquals('http://localhost/cas/login', $url);

        $url = $casLoginUrl(['redirect_url' => '/s/site']);
        $this->assertEquals('http://localhost/cas/login?redirect_url=/s/site', $url);

        $url = $casLoginUrl(['redirect_url' => '/s/site', 'gateway' => 'true']);
        $this->assertEquals('http://localhost/cas/login?redirect_url=/s/site&gateway=true', $url);

        $url = $casLoginUrl(['gateway' => 'true']);
        $this->assertEquals('http://localhost/cas/login?gateway=true', $url);
    }
}
