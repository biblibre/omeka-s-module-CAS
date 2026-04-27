<?php

namespace CAS\Test\CasService;

use CAS\Test\TestCase;
use CAS\Entity\CasUser;

class CasServiceTest extends TestCase
{
    public function testIsCasUser(): void
    {
        $services = $this->getServiceLocator();
        $casService = $services->get('CAS\CasService');

        $this->assertFalse($casService->isCasUser());
        $this->assertFalse($casService->isCasUser($this->identity()));

        $em = $services->get('Omeka\EntityManager');
        $casUser = new CasUser();
        $casUser->setId('cas_id');
        $casUser->setUser($this->identity());
        $em->persist($casUser);
        $em->flush();

        $this->assertTrue($casService->isCasUser());
        $this->assertTrue($casService->isCasUser($this->identity()));
    }

    public function testLoginUrl(): void
    {
        $services = $this->getServiceLocator();
        $casService = $services->get('CAS\CasService');

        $url = $casService->loginUrl();
        $this->assertEquals('http://localhost/cas/login', $url);

        $url = $casService->loginUrl(['redirect_url' => '/s/site']);
        $this->assertEquals('http://localhost/cas/login?redirect_url=/s/site', $url);

        $url = $casService->loginUrl(['redirect_url' => '/s/site', 'gateway' => 'true']);
        $this->assertEquals('http://localhost/cas/login?redirect_url=/s/site&gateway=true', $url);

        $url = $casService->loginUrl(['gateway' => 'true']);
        $this->assertEquals('http://localhost/cas/login?gateway=true', $url);
    }
}
