<?php

namespace CAS\Controller;

use CAS\Session\TicketStorage;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Psr\Log\LoggerInterface;

class SloController extends AbstractActionController
{
    private TicketStorage $ticketStorage;
    private LoggerInterface $logger;

    public function __construct(TicketStorage $ticketStorage, LoggerInterface $logger)
    {
        $this->ticketStorage = $ticketStorage;
        $this->logger = $logger;
    }

    public function receiveAction()
    {
        $request = $this->getRequest();
        if (!$request instanceof Request) {
            return $this->acknowledge();
        }

        $logoutRequest = $this->extractLogoutRequest($request);
        if ($logoutRequest === null) {
            $this->logger->warning('CAS SLO request received without a logoutRequest payload.');
            return $this->acknowledge();
        }

        $ticket = $this->extractSessionIndex($logoutRequest);
        if ($ticket === null) {
            $this->logger->warning('CAS SLO request received without a valid SessionIndex.');
            return $this->acknowledge();
        }

        $sessionId = $this->ticketStorage->remove($ticket);
        if ($sessionId === null) {
            $this->logger->info(sprintf('CAS SLO for ticket "%s" received but no matching session was found.', $ticket));
            return $this->acknowledge();
        }

        $sessionManager = Container::getDefaultManager();
        $saveHandler = $sessionManager->getSaveHandler();

        if ($saveHandler && method_exists($saveHandler, 'destroy')) {
            $saveHandler->destroy($sessionId);
        } else {
            $sessionManager->destroy();
        }

        $this->logger->info(sprintf('CAS SLO destroyed session "%s" for ticket "%s".', $sessionId, $ticket));

        return $this->acknowledge();
    }

    private function extractLogoutRequest(Request $request): ?string
    {
        $logoutRequest = $request->getPost('logoutRequest', null);
        if ($logoutRequest) {
            return (string) $logoutRequest;
        }

        $logoutRequest = $request->getQuery('logoutRequest', null);
        if ($logoutRequest) {
            return (string) $logoutRequest;
        }

        $content = $request->getContent();
        return $content !== '' ? $content : null;
    }

    private function extractSessionIndex(string $payload): ?string
    {
        if ($payload === '') {
            return null;
        }

        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($payload);
        if ($xml === false) {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            return null;
        }

        $nodes = $xml->xpath('//*[local-name()="SessionIndex"]');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$nodes) {
            return null;
        }

        $sessionIndex = trim((string) $nodes[0]);
        return $sessionIndex !== '' ? $sessionIndex : null;
    }

    private function acknowledge(): Response
    {
        $response = $this->getResponse();
        if (!$response instanceof Response) {
            $response = new Response();
        }

        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setContent('');
        return $response;
    }
}

