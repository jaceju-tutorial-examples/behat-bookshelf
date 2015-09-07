<?php

use App\Http\Controllers\BookshelfController;
use App\Services\BookshelfService;
use Illuminate\View\View;

class BookshelfControllerTest extends TestCase
{
    protected $service;

    protected $controller;

    public function setUp()
    {
        parent::setUp();

        // 通常 controller 有 constructor 的話，就可以考慮把它放在 setUp 裡
        $this->service = Mockery::mock(BookshelfService::class);
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
}
