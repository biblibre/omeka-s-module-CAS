<?php

namespace CAS\Session;

use CAS\Entity\CasTicket;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class TicketStorage
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function store(string $ticket, string $sessionId): void
    {
        if ($ticket === '' || $sessionId === '') {
            return;
        }

        $repository = $this->entityManager->getRepository(CasTicket::class);
        $entity = $repository->find($ticket);

        $now = new DateTimeImmutable('now');

        if ($entity === null) {
            $entity = new CasTicket();
            $entity->setTicket($ticket);
            $entity->setCreatedAt($now);
        }

        $entity->setSessionId($sessionId);
        $entity->setUpdatedAt($now);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function getSessionId(string $ticket): ?string
    {
        $entity = $this->entityManager->find(CasTicket::class, $ticket);

        return $entity ? $entity->getSessionId() : null;
    }

    public function remove(string $ticket): ?string
    {
        $entity = $this->entityManager->find(CasTicket::class, $ticket);
        if ($entity === null) {
            return null;
        }

        $sessionId = $entity->getSessionId();

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return $sessionId;
    }

    /**
     * @return string[]
     */
    public function removeBySessionId(string $sessionId): array
    {
        $repository = $this->entityManager->getRepository(CasTicket::class);
        $entities = $repository->findBy(['sessionId' => $sessionId]);

        if (!$entities) {
            return [];
        }

        $removedTickets = [];

        foreach ($entities as $entity) {
            $removedTickets[] = $entity->getTicket();
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();

        return $removedTickets;
    }
}

