<?php

use App\Book;
use App\CheckoutHistory;
use App\Services\BookshelfService;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookshelfServiceTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var BookshelfService
     */
    protected $service;

    /**
     * 不能用 setUp ，因為會在 DatabaseMigrations 前先執行而導致錯誤
     */
    protected function initFixtures()
    {
        $this->user = factory(User::class)->make([
            'id' => 1,
            'name' => 'Jace Ju',
            'email' => 'jaceju@example.com',
        ]);

        $book = factory(Book::class)->make();

        $this->service = new BookshelfService($this->user, $book);
    }

    public function testGetAllBooks()
    {
        // Arrange
        $this->initFixtures();
        factory(Book::class)->times(10)->create();

        // Act
        $books = $this->service->getAllBooks();

        // Assert
        $this->assertCount(10, $books);
    }

    public function testCheckoutBook()
    {
        // Arrange
        $this->initFixtures();
        $book = factory(Book::class)->create();

        // Act
        $this->service->checkoutBookById($book->id);

        // Assert
        $this->seeInDatabase('books', [
            'id' => $book->id,
            'available' => false,
        ]);
        $this->seeInDatabase('checkout_histories', [
            'user_id' => $this->user->id,
            'book_id' => $book->id,
            'returned' => false,
        ]);
    }

    public function testReturnBook()
    {
        // Arrange
        $this->initFixtures();
        $book = factory(Book::class)->create([
            'available' => false,
        ]);
        $history = factory(CheckoutHistory::class)->create([
            'user_id' => $this->user->id,
            'book_id' => $book->id,
            'returned' => false,
        ]);

        // Act
        $this->service->returnBookById($book->id);

        // Assert
        $this->seeInDatabase('books', [
            'id' => $book->id,
            'available' => true,
        ]);
        $this->seeInDatabase('checkout_histories', [
            'id' => $history->id,
            'user_id' => $this->user->id,
            'book_id' => $book->id,
            'returned' => true,
        ]);
    }
}