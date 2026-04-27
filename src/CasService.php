<?php

namespace CAS;

use CAS\Entity\CasUser;
use Doctrine\ORM\EntityManager;
use Laminas\Authentication\AuthenticationService;
use Laminas\View\HelperPluginManager;
use Omeka\Entity\User;

class CasService
{
    public function __construct(
        protected AuthenticationService $authenticationService,
        protected EntityManager $entityManager,
        protected HelperPluginManager $helperPluginManager
    ) {}

    /**
     * Returns true if user used CAS to log in
     *
     * @param ?User $user user to check. if not given or null, check the
     *                    currently authenticated user
     */
    public function isCasUser(?User $user = null): bool
    {
        $user ??= $this->authenticationService->getIdentity();
        if (!$user) {
            return false;
        }

        $casUserRepository = $this->entityManager->getRepository(CasUser::class);
        $casUser = $casUserRepository->findOneBy(['user' => $user]);

        return $casUser !== null;
    }

    public function loginUrl(array $options = [])
    {
        $url = $this->helperPluginManager->get('Url');

        $query = [];
        if (!empty($options['redirect_url'])) {
            $query['redirect_url'] = $options['redirect_url'];
        }
        if (!empty($options['gateway'])) {
            $query['gateway'] = $options['gateway'];
        }

        return $url('cas/login', [], ['force_canonical' => true, 'query' => $query]);
    }
}
