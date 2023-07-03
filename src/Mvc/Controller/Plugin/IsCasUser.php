<?php declare(strict_types=1);

namespace CAS\Mvc\Controller\Plugin;

use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Omeka\Entity\User;

class IsCasUser extends AbstractPlugin
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Check if a user is authenticated by cas.
     */
    public function __invoke(?User $user): bool
    {
        return $user
            && $this->entityManager->getReference(\CAS\Entity\CasUser::class, $user->getId());
    }
}
