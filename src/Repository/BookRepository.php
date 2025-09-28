<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Find books by author.
     *
     * @return Book[]
     */
    public function findByAuthor(string $author): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.author LIKE :author')
            ->setParameter('author', '%' . $author . '%')
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find books published recently (within last days).
     *
     * @return Book[]
     */
    public function findRecentBooks(int $days = 30): array
    {
        $date = new \DateTimeImmutable('-' . $days . ' days');

        return $this->createQueryBuilder('b')
            ->andWhere('b.createdAt >= :date')
            ->setParameter('date', $date)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search books by title or author.
     *
     * @return Book[]
     */
    public function search(string $query): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.title LIKE :query OR b.author LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
