<?php

namespace CAS\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use CAS\CasService;

class Cas extends AbstractHelper
{
    public function __construct(protected CasService $casService)
    {
    }

    public function __invoke(): CasService
    {
        return $this->casService;
    }
}
