<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class BookServiceTest extends AbstractTestCase
{
    public function testGetBooksByCategoryNotFound(): void
    {
        $bookRepository = $this->createMock(BookRepository::class);
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);

        $bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false);

        $this->expectException(BookCategoryNotFoundException::class);

        (new BookService($bookRepository, $bookCategoryRepository))->getBooksByCategory(130);
    }

    public function testGetBooksByCategory(): void
    {
        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects($this->once())
            ->method('findBookByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true);

        $services = new BookService($bookRepository, $bookCategoryRepository);
        $expected = new BookListResponse([$this->createBookItemModel()]);

        $this->assertEquals($expected, $services->getBooksByCategory(130));
    }

    private function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('https://localhost/test.png')
            ->setCategories(new ArrayCollection())
            ->setPublicationDate(new \DateTime('2020-10-10'));
        $this->setEntityId($book, 123);

        return $book;
    }

    private function createBookItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('https://localhost/test.png')
            ->setPublicationDate((new \DateTime('2020-10-10'))->getTimestamp());
    }
}
