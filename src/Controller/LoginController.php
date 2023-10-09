<?php

/*
 * Copyright 2020 BibLibre
 *
 * This file is part of CAS.
 *
 * CAS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CAS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CAS.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace CAS\Controller;

use CAS\Entity\CasUser;
use Doctrine\ORM\EntityManager;
use Omeka\Entity\User;
use Omeka\Permissions\Acl;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Client as HttpClient;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\Uri\UriFactory;

class LoginController extends AbstractActionController
{
    protected $httpClient;
    protected $entityManager;
    protected $authenticationService;

    public function __construct(HttpClient $httpClient, EntityManager $entityManager, AuthenticationService $authenticationService)
    {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->authenticationService = $authenticationService;
    }

    public function loginAction()
    {
        $redirectUrl = $this->getRedirectUrl();
        if ($redirectUrl) {
            $session = Container::getDefaultManager()->getStorage();
            $session->offsetSet('redirect_url', $redirectUrl);
        }

        $casUrl = UriFactory::factory(sprintf('%s/login', $this->getCasUrlSetting()));
        $query = [
            'service' => $this->getServiceUrl(),
        ];

        $gateway = $this->params()->fromQuery('gateway');
        if ($gateway) {
            $query['gateway'] = 'true';
        }

        $casUrl->setQuery($query);

        return $this->redirect()->toUrl($casUrl->toString());
    }

    public function validateAction()
    {
        $sessionManager = Container::getDefaultManager();
        $ticket = $this->params()->fromQuery('ticket');
        if ($ticket) {
            $response = $this->serviceValidate($ticket);
            if (!$response->isOk()) {
                $this->logger()->err('CAS ticket validation failed. CAS Server response: ' . $response->renderStatusLine());
                $this->messenger()->addError($this->translate('Failed to validate CAS ticket'));
                return $this->redirect()->toRoute('login');
            }

            $body = $response->getBody();
            $xml = simplexml_load_string($body, 'SimpleXMLElement', 0, 'http://www.yale.edu/tp/cas');
            $casResponse = json_decode(json_encode($xml), true);
            if (!isset($casResponse['authenticationSuccess'])) {
                $this->logger()->err('CAS ticket validation failed. CAS Server response: ' . $body);
                $this->messenger()->addError($this->translate('Failed to validate CAS ticket'));
                return $this->redirect()->toRoute('login');
            }

            $cas = $casResponse['authenticationSuccess'];

            $user = $this->getUser($cas);
            if (!$user->isActive()) {
                $this->messenger()->addError($this->translate('User is inactive'));
                return $this->redirect()->toRoute('login');
            }

            $sessionManager->regenerateId();

            $this->authenticationService->getStorage()->write($user);
            $this->getEventManager()->trigger('cas.user.login', $user, [
                'user' => $cas['user'],
                'attributes' => $cas['attributes'] ?? [],
            ]);
        }

        $redirectUrl = $this->getRedirectUrl();
        if ($redirectUrl) {
            return $this->redirect()->toUrl($redirectUrl);
        }

        return $this->userIsAllowed('Omeka\Controller\Admin\Index', 'browse')
            ? $this->redirect()->toRoute('admin')
            : $this->redirect()->toRoute('top');
    }

    protected function serviceValidate($ticket)
    {
        $serviceValidateUrl = $this->getCasUrlSetting() . '/serviceValidate';
        $httpClient = $this->httpClient;
        $httpClient->reset();
        $httpClient->setUri($serviceValidateUrl);
        $httpClient->setParameterGet([
            'service' => $this->getServiceUrl(),
            'ticket' => $ticket,
        ]);

        return $httpClient->send();
    }

    protected function getUser($cas)
    {
        $em = $this->entityManager;
        $events = $this->getEventManager();
        $eventArgs = [
            'user' => $cas['user'],
            'attributes' => $cas['attributes'] ?? [],
        ];

        $user_id_attribute = $this->settings()->get('cas_user_id_attribute');
        if ($user_id_attribute) {
            $cas_user_id = $cas['attributes'][$user_id_attribute] ?? null;
        } else {
            $cas_user_id = $cas['user'] ?? null;
        }

        if (!isset($cas_user_id)) {
            throw new \Exception('User identifier not found in CAS response');
        }

        $user_name_attribute = $this->settings()->get('cas_user_name_attribute');
        if ($user_name_attribute && isset($cas['attributes'][$user_name_attribute])) {
            $cas_user_name = $cas['attributes'][$user_name_attribute];
        } else {
            $cas_user_name = $cas_user_id;
        }

        $user_email_attribute = $this->settings()->get('cas_user_email_attribute');
        if ($user_email_attribute && isset($cas['attributes'][$user_email_attribute])) {
            $cas_user_email = $cas['attributes'][$user_email_attribute];
        } else {
            $cas_user_email = $cas_user_id;
        }

        $casUser = $em->find('CAS\Entity\CasUser', $cas_user_id);
        if (!$casUser) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $cas_user_email]);
            if (!$user) {
                $user = new User();
                $user->setName($cas_user_name);
                $user->setEmail($cas_user_email);
                $user->setRole($this->settings()->get('cas_role', Acl::ROLE_RESEARCHER));
                $user->setIsActive(true);

                $events->trigger('cas.user.create.pre', $user, $eventArgs);

                $em->persist($user);
                $em->flush();

                $events->trigger('cas.user.create.post', $user, $eventArgs);
            } else {
                $events->trigger('cas.user.update.pre', $user, $eventArgs);

                $em->persist($user);
                $em->flush();

                $events->trigger('cas.user.update.post', $user, $eventArgs);
            }

            $casUser = new CasUser();
            $casUser->setId($cas_user_id);
            $casUser->setUser($user);

            $em->persist($casUser);
            $em->flush();
        } else {
            $user = $casUser->getUser();

            $events->trigger('cas.user.update.pre', $user, $eventArgs);

            $em->persist($user);
            $em->flush();

            $events->trigger('cas.user.update.post', $user, $eventArgs);
        }

        return $user;
    }

    protected function getServiceUrl()
    {
        return $this->url()->fromRoute('cas/validate', [], ['force_canonical' => true]);
    }

    protected function getCasUrlSetting()
    {
        return trim($this->settings()->get('cas_url'));
    }

    protected function getRedirectUrl(): ?string
    {
        $redirectQuery = $this->params()->fromQuery('redirect_url') ?: null;
        $redirectSession = Container::getDefaultManager()->getStorage()->offsetGet('redirect_url');

        $mode = $this->settings()->get('cas_redirect_mode');
        if ($mode === 'query') {
            return $redirectQuery;
        } elseif ($mode === 'session') {
            return $redirectSession;
        } elseif ($mode === 'query_then_session') {
            return $redirectQuery ?? $redirectSession;
        } elseif ($mode === 'session_then_query') {
            return $redirectSession ?? $redirectQuery;
        } else {
            return null;
        }
    }
}
