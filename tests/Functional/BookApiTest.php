<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional tests for the Book API.
 *
 * Database isolation is handled automatically by DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension
 * which wraps each test in a database transaction that gets rolled back after completion.
 */
class BookApiTest extends WebTestCase
{
    private function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testGetBooksCollection(): void
    {
        $client = static::createClient([], [
            'CONTENT_TYPE' => 'application/ld+json',
        ]);

        // Create a test book
        $book = new Book();
        $book->setTitle('Test Book');
        $book->setAuthor('Test Author');
        $book->setDescription('Test Description');

        $entityManager = $this->getEntityManager();
        $entityManager->persist($book);
        $entityManager->flush();

        $client->request('GET', '/api/books');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('member', $responseData);
        $this->assertCount(1, $responseData['member']);
        $this->assertSame('Test Book', $responseData['member'][0]['title']);
    }

    public function testCreateBook(): void
    {
        $client = static::createClient([],
            [
                'CONTENT_TYPE' => 'application/ld+json',
            ]
        );

        $bookData = [
            'title' => 'New Book',
            'author' => 'New Author',
            'description' => 'A new book description',
            'isbn' => '978-0-306-40615-7',
        ];

        $client->request('POST', '/api/books', [], [], [], json_encode($bookData));

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame($bookData['title'], $responseData['title']);
        $this->assertSame($bookData['author'], $responseData['author']);
        $this->assertArrayHasKey('id', $responseData);
    }

    public function testGetSingleBook(): void
    {
        $client = static::createClient();

        // Create a test book
        $book = new Book();
        $book->setTitle('Single Book');
        $book->setAuthor('Single Author');

        $entityManager = $this->getEntityManager();
        $entityManager->persist($book);
        $entityManager->flush();
        $unitOfWork = $entityManager->getUnitOfWork();
        $identifier = $unitOfWork->getEntityIdentifier($book);

        $client->request('GET', '/api/books/'.$identifier['id']);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Single Book', $responseData['title']);
        $this->assertSame('Single Author', $responseData['author']);
    }

    public function testUpdateBook(): void
    {
        $client = static::createClient([], [
            'CONTENT_TYPE' => 'application/ld+json',
        ]);

        // Create a test book
        $book = new Book();
        $book->setTitle('Original Title');
        $book->setAuthor('Original Author');

        $entityManager = $this->getEntityManager();
        $entityManager->persist($book);
        $entityManager->flush();

        $updateData = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
        ];

        $client->request('PUT', '/api/books/'.$book->getId(), [], [], [], json_encode($updateData));

        $this->assertResponseIsSuccessful();

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Updated Title', $responseData['title']);
        $this->assertSame('Updated Author', $responseData['author']);
    }

    public function testDeleteBook(): void
    {
        $client = static::createClient([], [
            'CONTENT_TYPE' => 'application/ld+json',
        ]);

        // Create a test book
        $book = new Book();
        $book->setTitle('Book to Delete');
        $book->setAuthor('Delete Author');

        $entityManager = $this->getEntityManager();
        $entityManager->persist($book);
        $entityManager->flush();

        $bookId = $book->getId();

        $client->request('DELETE', '/api/books/'.$bookId);

        $this->assertResponseStatusCodeSame(204);

        // Verify the book is deleted
        $client->request('GET', '/api/books/'.$bookId);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreateBookValidation(): void
    {
        $client = static::createClient([], [
            'CONTENT_TYPE' => 'application/ld+json',
        ]);

        $invalidBookData = [
            'title' => '', // Empty title should fail validation
            'author' => '', // Empty author should fail validation
        ];

        $client->request('POST', '/api/books', [], [], [], json_encode($invalidBookData));

        $this->assertResponseStatusCodeSame(422); // Unprocessable Entity
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
