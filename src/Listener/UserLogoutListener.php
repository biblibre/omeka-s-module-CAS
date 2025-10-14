<?php

namespace CAS\Listener;

use CAS\Session\TicketStorage;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Omeka\Settings\Settings;
use Psr\Container\ContainerInterface;

class UserLogoutListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    private Settings $settings;
    private TicketStorage $ticketStorage;
    private ContainerInterface $services;
    private ?AuthenticationService $authenticationService = null;

    public function __construct(Settings $settings, TicketStorage $ticketStorage, ContainerInterface $services)
    {
        $this->settings = $settings;
        $this->ticketStorage = $ticketStorage;
        $this->services = $services;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedManager = $events->getSharedManager();
        $this->listeners[] = $sharedManager->attach(AbstractController::class, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
        $this->listeners[] = $sharedManager->attach('*', 'user.logout', [$this, 'onUserLogout'], 100);
    }

    public function onDispatch(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();
        if (!$routeMatch) {
            return;
        }

        $matchedRouteName = $routeMatch->getMatchedRouteName();
        $action = $routeMatch->getParam('action');
        if ($matchedRouteName !== 'logout' && $action !== 'logout') {
            return;
        }

        if (!(bool) $this->settings->get('cas_global_logout')) {
            return;
        }

        $casUrl = trim((string) $this->settings->get('cas_url'));
        if ($casUrl === '') {
            return;
        }

        $controller = $event->getTarget();
        if (!$controller instanceof AbstractController) {
            return;
        }

        $sessionManager = Container::getDefaultManager();
        $sessionManager->start();

        $this->getAuthenticationService()->clearIdentity();

        $controller->getEventManager()->trigger('user.logout');
        $sessionManager->destroy();

        $logoutUrl = $this->buildLogoutUrl($controller, $casUrl);
        $response = $controller->redirect()->toUrl($logoutUrl);

        $event->setResult($response);
        $event->stopPropagation(true);

        return $response;
    }

    public function onUserLogout(EventInterface $event): void
    {
        $sessionManager = Container::getDefaultManager();
        $sessionManager->start();

        $sessionId = $sessionManager->getId();
        if ($sessionId === '') {
            return;
        }

        $this->ticketStorage->removeBySessionId($sessionId);

        $sessionStorage = $sessionManager->getStorage();
        if ($sessionStorage->offsetExists('cas_service_tickets')) {
            $sessionStorage->offsetUnset('cas_service_tickets');
        }
    }

    private function buildLogoutUrl(AbstractController $controller, string $casUrl): string
    {
        $casBaseUrl = rtrim($casUrl, '/');
        $redirectService = trim((string) $this->settings->get('cas_logout_redirect_service'));

        if ($redirectService === '') {
            $redirectService = $controller->url()->fromRoute('top', [], ['force_canonical' => true]);
        }

        if ($redirectService === '') {
            return $casBaseUrl . '/logout';
        }

        return sprintf('%s/logout?service=%s', $casBaseUrl, rawurlencode($redirectService));
    }

    private function getAuthenticationService(): AuthenticationService
    {
        if ($this->authenticationService === null) {
            $this->authenticationService = $this->services->get('Omeka\AuthenticationService');
        }

        return $this->authenticationService;
    }
}

