# 重構

## 加入權限

在 `BookshelfController` 類別中加入：

```php
    public function __construct()
    {
        $this->middleware('auth');
    }
```

執行 `behat` 應通過所有 scenarios 。

## 改用注入取代 Facade

在 `BookshelfController` 類別中引用：

```php
use Illuminate\Contracts\Auth\Authenticatable;
```

然後在建構子中注入：

```php
    /**
     * @var Authenticatable
     */
    private $user;

    public function __construct(Authenticatable $user)
    {
        $this->middleware('auth');
        $this->user = $user;
    }
```

將所有的 `Auth::user()` 替換成 `$this->user` 。

## 將 model 相關操作提煉成方法

利用 PhpStorm 的 extract method 功能，將：

```php
$books = Book::all();
```

提煉成：

```php
$books = $this->getAllBooks();
```

及：

```php
/**
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getAllBooks()
{
    $books = Book::all();
    return $books;
}
```

將：

```php
$book = Book::findOrFail($bookId);
/** @var Book $book */
$book->available = false;
$book->save();
$book->checkoutHistories()
    ->create([
        'user_id' => $this->user->id,
    ]);
```

提煉成：

```php
$this->checkoutBookById($bookId);
```

及：

```php
/**
 * @param $bookId
 */
public function checkoutBookById($bookId)
{
    $book = Book::findOrFail($bookId);
    /** @var Book $book */
    $book->available = false;
    $book->save();
    $book->checkoutHistories()
        ->create([
            'user_id' => $this->user->id,
        ]);
}
```

將：

```php
$book = Book::findOrFail($bookId);
/** @var Book $book */
$book->available = true;
$book->save();
$checkHistory = $book->checkoutHistories()
    ->notReturnedByUser($this->user->id)
    ->first();
$checkHistory->returned = true;
$checkHistory->save();
```

提煉成：

```php
$this->returnBookById($bookId);
```

及：

```php
/**
 * @param $bookId
 */
public function returnBookById($bookId)
{
    $book = Book::findOrFail($bookId);
    /** @var Book $book */
    $book->available = true;
    $book->save();
    $checkHistory = $book->checkoutHistories()
        ->notReturnedByUser($this->user->id)
        ->first();
    $checkHistory->returned = true;
    $checkHistory->save();
}
```

### 引入 service 類別

建立 `app/BookshelfService.php` ，內容為 `BookshelfService` 類別。其建構子參考 `BookshelfController` 類別注入 `Authenticatable` 物件：

```php
use Illuminate\Contracts\Auth\Authenticatable;

class BookshelfService
{
    /**
     * @var Authenticatable
     */
    private $user;

    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
```

將 `BookshelfController::getAllBooks` 、 `BookshelfController::checkoutBookById` 及 `BookshelfController::returnBookById` 三個方法移到 `BookshelfService` 類別中。

`BookshelfController` 類別的建構子改為注入 `BookshelfService` 物件，並移除原來的 `Authenticatable` 物件：

```php
use App\BookshelfService;

class BookshelfController extends Controller
{
    /**
     * @var BookshelfService
     */
    private $service;

    public function __construct(BookshelfService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }
```

將 `BookshelfController` 類別中的：

```php
$this->getAllBooks();
```

```php
$this->checkoutBookById($bookId);
```

```php
$this->returnBookById($bookId);
```

分別改為呼叫 `BookshelfService` 物件的方法：

```php
$this->service->getAllBooks();
```

```php
$this->service->checkoutBookById($bookId);
```

```php
$this->service->returnBookById($bookId);
```

下一步：[加入測試](tips/08-testing.md)。