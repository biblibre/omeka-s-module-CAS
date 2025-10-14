<?php

namespace CAS\Entity;

use DateTimeImmutable;
use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 * @Table(name="cas_ticket")
 */
class CasTicket extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="string", length=255)
     */
    protected $ticket;

    /**
     * @Column(name="session_id", type="string", length=255)
     */
    protected $sessionId;

    /**
     * @Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    public function getTicket(): string
    {
        return $this->ticket;
    }

    public function getId()
    {
        return $this->ticket;
    }

    public function setTicket(string $ticket): void
    {
        $this->ticket = $ticket;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
