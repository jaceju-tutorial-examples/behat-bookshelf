<?php

use App\Http\Controllers\BookshelfController;
use App\Services\BookshelfService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;
use Illuminate\Http\Request;

class BookshelfControllerTest extends TestCase
{
    protected $request;

    /** @var Mockery\MockInterface */
    protected $service;

    protected $controller;

    public function setUp()
    {
        parent::setUp();

        // 通常 controller 有 constructor 的話，就可以考慮把它放在 setUp 裡
        $this->service = Mockery::mock(BookshelfService::class);
        $this->request = Mockery::mock(Request::class);
        $this->controller = new BookshelfController($this->service);
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testIndex()
    {
        // Arrange
        $expected = [1, 2, 3, 4, 5];
        $this->service->shouldReceive('getAllBooks')
            ->once()
            ->withNoArgs()
            ->andReturn($expected);

        // Act
        $view = $this->controller->index();
        /** @var View $view */
        $actual = $view->books;

        // Assert
        $this->assertEquals($expected, $actual);
    }

    public function testCheckout()
    {
        // Arrange
        $bookId = 1;
        $this->request->shouldReceive('get')
            ->once()
            ->with('book_id')
            ->andReturn($bookId);
        $this->service->shouldReceive('checkoutBookById')
            ->once()
            ->with($bookId);

        // Act
        $response = $this->controller->checkout($this->request);

        // Assert
        // config/app.php/url
        $this->assertEquals(302, $response->status());
        $this->assertEquals('http://localhost', $response->getTargetUrl());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testCheckoutFail()
    {
        // Arrange
        $bookId = 1;
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('get')
            ->once()
            ->with('book_id')
            ->andReturn($bookId);
        $this->service->shouldReceive('checkoutBookById')
            ->once()
            ->with($bookId)
            ->andThrow(ModelNotFoundException::class);

        // Act
        $this->controller->checkout($request);
    }

    public function testReturnBook()
    {
        // Arrange
        $bookId = 1;
        $this->request->shouldReceive('get')
            ->once()
            ->with('book_id')
            ->andReturn($bookId);
        $this->service->shouldReceive('returnBookById')
            ->once()
            ->with($bookId);

        // Act
        $response = $this->controller->returnBook($this->request);

        // Assert
        // config/app.php/url
        $this->assertEquals(302, $response->status());
        $this->assertEquals('http://localhost', $response->getTargetUrl());
    }
}
