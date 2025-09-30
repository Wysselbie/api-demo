<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testBookCreation(): void
    {
        $book = new Book();

        $this->assertNull($book->getId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $book->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $book->getUpdatedAt());
    }

    public function testBookSettersAndGetters(): void
    {
        $book = new Book();
        $originalUpdatedAt = $book->getUpdatedAt();

        // Small delay to ensure timestamp difference
        usleep(1000);

        $book->setTitle('Test Book');
        $book->setAuthor('Test Author');
        $book->setDescription('Test Description');
        $book->setIsbn('978-0123456789');

        $this->assertSame('Test Book', $book->getTitle());
        $this->assertSame('Test Author', $book->getAuthor());
        $this->assertSame('Test Description', $book->getDescription());
        $this->assertSame('978-0123456789', $book->getIsbn());

        // Verify that updatedAt was modified
        $this->assertNotEquals($originalUpdatedAt, $book->getUpdatedAt());
        $this->assertGreaterThan($originalUpdatedAt, $book->getUpdatedAt());
    }

    public function testBookTitleUpdateChangesUpdatedAt(): void
    {
        $book = new Book();
        $originalUpdatedAt = $book->getUpdatedAt();

        // Small delay to ensure timestamp difference
        usleep(1000);

        $book->setTitle('New Title');

        $this->assertGreaterThan($originalUpdatedAt, $book->getUpdatedAt());
    }

    public function testBookOptionalFields(): void
    {
        $book = new Book();

        $this->assertNull($book->getDescription());
        $this->assertNull($book->getIsbn());

        $book->setDescription(null);
        $book->setIsbn(null);

        $this->assertNull($book->getDescription());
        $this->assertNull($book->getIsbn());
    }
}
