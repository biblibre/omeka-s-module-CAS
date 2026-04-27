<?php

namespace CAS\Mvc\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use CAS\CasService;

class Cas extends AbstractPlugin
{
    public function __construct(protected CasService $casService)
    {
    }

    public function __invoke(): CasService
    {
        return $this->casService;
    }
}
