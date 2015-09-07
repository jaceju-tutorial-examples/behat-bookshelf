<?php

use App\Http\Controllers\BookshelfController;
use App\Services\BookshelfService;

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
}
