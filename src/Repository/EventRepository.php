<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Find events by aggregate.
     *
     * @return Event[]
     */
    public function findByAggregate(string $aggregateId, string $aggregateType): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.aggregateId = :aggregateId')
            ->andWhere('e.aggregateType = :aggregateType')
            ->setParameter('aggregateId', $aggregateId)
            ->setParameter('aggregateType', $aggregateType)
            ->orderBy('e.version', 'ASC')
            ->addOrderBy('e.occurredAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events by event type.
     *
     * @return Event[]
     */
    public function findByEventType(string $eventType): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.eventType = :eventType')
            ->setParameter('eventType', $eventType)
            ->orderBy('e.occurredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events that occurred after a specific date.
     *
     * @return Event[]
     */
    public function findEventsAfter(\DateTimeImmutable $dateTime): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.occurredAt > :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->orderBy('e.occurredAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events that occurred before a specific date.
     *
     * @return Event[]
     */
    public function findEventsBefore(\DateTimeImmutable $dateTime): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.occurredAt < :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->orderBy('e.occurredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events by type and occurred after a specific date.
     *
     * @return Event[]
     */
    public function findEventsByTypeAfter(string $eventType, \DateTimeImmutable $dateTime): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.eventType = :eventType')
            ->andWhere('e.occurredAt > :dateTime')
            ->setParameter('eventType', $eventType)
            ->setParameter('dateTime', $dateTime)
            ->orderBy('e.occurredAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find events by type and occurred before a specific date.
     *
     * @return Event[]
     */
    public function findEventsByTypeBefore(string $eventType, \DateTimeImmutable $dateTime): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.eventType = :eventType')
            ->andWhere('e.occurredAt < :dateTime')
            ->setParameter('eventType', $eventType)
            ->setParameter('dateTime', $dateTime)
            ->orderBy('e.occurredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the latest version for an aggregate.
     */
    public function getLatestVersion(string $aggregateId, string $aggregateType): int
    {
        $result = $this->createQueryBuilder('e')
            ->select('MAX(e.version)')
            ->andWhere('e.aggregateId = :aggregateId')
            ->andWhere('e.aggregateType = :aggregateType')
            ->setParameter('aggregateId', $aggregateId)
            ->setParameter('aggregateType', $aggregateType)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) ($result ?? 0);
    }
}
