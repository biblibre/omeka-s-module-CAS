<?php

namespace CAS\View\Helper;

use CAS\Mvc\Controller\Plugin\IsCasUser as IsCasUserPlugin;
use Laminas\View\Helper\AbstractHelper;
use Omeka\Entity\User;

class IsCasUser extends AbstractHelper
{
    /**
     * @var \CAS\Mvc\Controller\Plugin\IsCasUser
     */
    protected $isCasUserPlugin;

    public function __construct(IsCasUserPlugin $isCasUser)
    {
        $this->isCasUserPlugin = $isCasUser;
    }

    /**
     * Check if a user is authenticated by cas.
     */
    public function __invoke(?User $user): bool
    {
        return $user
            && $this->isCasUserPlugin->__invoke($user);
    }
}