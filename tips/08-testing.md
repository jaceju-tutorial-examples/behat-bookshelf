# 加入測試

## 加入 BookshelfService 的測試類別

刪除 `tests/ExampleTest.php` 。

修改 `phpunit.xml` ，在 `<php>` 區段中加入：

```xml
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_SOURCE" value=":memory:"/>
```

建立 `tests/BookshelfServiceTest.php` ，內容如下：

```php
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookshelfServiceTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
}
```

## 新增 `BookshelfService::getAllBooks` 方法的測試

新增第一個測試案例，在 `BookshelfServiceTest` 類別中加入：

```php

    /**
     * @var User
     */
    protected $user;

    /**
     * @var BookshelfService
     */
    protected $service;

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
```

要記得引用類別：

```php
use App\Book;
use App\Services\BookshelfService;
use App\User;
```

執行 `phpunit` ：

```bash
./vendor/bin/phpunit
```

## 新增 `BookshelfService::checkoutBookById` 方法的測試

在 `BookshelfServiceTest` 類別中加入：

```php

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
```

執行 `phpunit` 。

## 新增 `BookshelfService::returnBookById` 方法的測試

在 `BookshelfServiceTest` 類別中加入：

```php
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
```

執行 `phpunit` 。

## 加入 BookshelfController 的測試類別

建立 `tests/BookshelfControllerTest.php` ，內容如下：

```php
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
```

## 新增 `BookshelfController::index` 方法的測試

在 `BookshelfControllerTest` 類別中加入：

```php
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
        $actual = $view->books;

        // Assert
        $this->assertEquals($expected, $actual);
    }
```

執行 `phpunit` 。

## 新增 `BookshelfController::checkout` 方法的測試

在 `BookshelfControllerTest` 類別中加入：

```php
    public function testCheckout()
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
            ->with($bookId);

        // Act
        $response = $this->controller->checkout($request);

        // Assert
        // config/app.php/url
        $this->assertEquals(302, $response->status());
        $this->assertEquals('http://localhost', $response->getTargetUrl());
    }
```

要記得引入 `Request` 的完整類別：

```php
use Illuminate\Http\Request;
```

執行 `phpunit` 。

## 新增 `BookshelfController::returnBook` 方法的測試

在 `BookshelfControllerTest` 類別中加入：

```php
    public function testReturnBook()
    {
        // Arrange
        $bookId = 1;
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('get')
            ->once()
            ->with('book_id')
            ->andReturn($bookId);
        $this->service->shouldReceive('returnBookById')
            ->once()
            ->with($bookId);

        // Act
        $response = $this->controller->returnBook($request);

        // Assert
        // config/app.php/url
        $this->assertEquals(302, $response->status());
        $this->assertEquals('http://localhost', $response->getTargetUrl());
    }
```

執行 `phpunit` 。

## 重構測試類別

在 `BookshelfControllerTest` 類別中，新增一個 `$requst` 屬性：

```php
    protected $request;
```

在 `setUp` 方法裡加入：

```php
        $this->request = Mockery::mock(Request::class);
```

最後把：

```php
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('get')
```

```php
        $response = $this->controller->returnBook($request);
```

分別改成：

```php
        $this->request->shouldReceive('get')
```

```php
        $response = $this->controller->returnBook($this->request);
```

## 修改 `BookshelfController::checkout` 方法流程並加入測試

在 `BookshelfControllerTest` 類別，加入 `testCheckoutFail` 方法，並複製 `testCheckout` 方法的內容，再修改成：

```php
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
```

要記得引入 `ModelNotFoundException` 的完整類別名稱：

```
use Illuminate\Database\Eloquent\ModelNotFoundException;
```

這個測試案例的目的在模擬找不到對應的 model 時，應該丟出 500 的 `HttpException` 。

執行 `phpunit` ，應出現測試失敗的狀況。

修改 `BookshelfController::checkout` 方法，將：

```
$this->service->checkoutBookById($bookId);
```

改為：

```php
try {
    $this->service->checkoutBookById($bookId);
} catch (ModelNotFoundException $e) {
    abort(500);
}
```

執行 `phpunit` ，測試應該成功。

在 `BookshelfController::returnBook` 方法上套用同樣的步驟。

下一步：[整合 Travis CI](tips/09-travis-ci.md) 。
