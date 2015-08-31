# 書籍借閱功能

## 提煉及引用 trait

建立 `features/bootstrap/Authentication.php` ，加入一個 `Authentication` 的 trait ；再將 `MembershipContext::registeredAccount` 與 `MembershipContext::iHaveLoggedIn` 這兩個方法搬到新 trait 中：

```php
use Illuminate\Support\Facades\Auth;

trait Authentication
{
    /**
     * @Given 帳號 :name :email 已註冊
     */
    public function registeredAccount($name, $email)
    {
        factory(App\User::class)->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * @Then 登入系統
     */
    public function iHaveLoggedIn()
    {
        $this->assertTrue(Auth::check());
    }
}
```

最後在 `BookshelfContext` 類別中，引用 `Authentication` trait ：

```php
    use Authentication;
```

## 書籍列表的 Background

因為 Background 需要配合 Scenario 才能產生 step definitions ，所以不能單獨加入。

建立 `features/bookshelf/Bookshelf.feature` ，內容如下：

```gherkin
Feature: 使用者可以借還書籍
    In order to 借還書籍
    As a 使用者
    I want to 查看書籍列表、借書及還書

    Background:
        Given 用帳號 "Jace Ju" "jaceju@example.com" 登入系統
        And 帳號 "Taylor Otwell" "taylorotwell@example.com" 已註冊
        And 帳號 "Jeffrey Way" "jeffreyway@example.com" 已註冊
        And 書架上現有書籍
            | 書籍名稱                | 出借狀況 |
            | 專案管理實務              | 可借出  |
            | HTML5 + CSS3 專用網站設計 | 可借出  |
            | JavaScript 學習手冊     | 可借出  |
            | 精通 VI               | 可借出  |
            | PHP 聖經              | 可借出  |
        And 書籍 "專案管理實務" 已被 "taylorotwell@example.com" 借出
        And 書籍 "精通 VI" 已被 "jeffreyway@example.com" 借出

    Scenario: 使用者可查看書籍列表及出借狀況
        When 進入首頁
        Then 顯示書籍清單、出借狀況
            | 書籍名稱                | 出借狀況 |
            | 專案管理實務              | 已借出  |
            | HTML5 + CSS3 專用網站設計 | 可借出  |
            | JavaScript 學習手冊     | 可借出  |
            | 精通 VI               | 已借出  |
            | PHP 聖經              | 可借出  |
```

執行 `behat --append-snippets` 在 `BookshelfContext` 類別中新增 step definitions ，並修改方法名稱：

```php

    /**
     * @Given 用帳號 :name :email 登入系統
     */
    public function iHaveLoggedInAs($name, $email)
    {
        throw new PendingException();
    }

    /**
     * @Given 書架上現有書籍
     */
    public function onShelfBooks(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @Given 書籍 :bookName 已被 :email 借出
     */
    public function bookCheckedOutByUser($bookName, $email)
    {
        throw new PendingException();
    }

    /**
     * @When 進入首頁
     */
    public function visitHome()
    {
        throw new PendingException();
    }

    /**
     * @Then 顯示書籍清單、出借狀況
     */
    public function booksList(TableNode $table)
    {
        throw new PendingException();
    }
```

### 第一個 step ：用帳號自動登入系統

修改 `BookshelfContext::iHaveLoggedInAs` 方法：

```php
    public function iHaveLoggedInAs($name, $email)
    {
        $this->registeredAccount($name, $email);
        $this->signInAs($email);
        $this->iHaveLoggedIn();
    }
```

在 `Authentication` trait 中，加入 `signInAs` 方法：

```php

    /**
     * @param string $email
     * @param string $password
     */
    public function signInAs($email, $password = 'password')
    {
        Auth::attempt([
            'email' => $email,
            'password' => $password,
        ]);
    }
```

執行 `behat` ，應該通過第一個 step 。

### 第二個 step ：書架上現有書籍

建立一個 `Book` model ：

```bash
php artisan make:model Book -m
```

編輯 `app/Book.php` ，將內容修改如下：

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'name',
        'available',
    ];
}
```

編輯 `database/migrations/2015_09_01_172658_create_books_table.php` ，在 schema 中加入 `name` 及  `available` 兩個欄位：

```php
Schema::create('books', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->boolean('available');
    $table->timestamps();
});
```

編輯 `database/factories/ModelFactory.php` ，加入以下內容：

```php
$factory->define(App\Book::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->sentence(6),
        'available' => true,
    ];
});
```

編輯 `BookshelfContext::onShelfBooks` 方法，改成：

```php
    /**
     * @Given 書架上現有書籍
     */
    public function onShelfBooks(TableNode $table)
    {
        $map = [
            '可借出' => true,
            '已借出' => false,
        ];

        foreach ($table as $book) {
            factory(Book::class)->create([
                'name' => $book['書籍名稱'],
                'available' => $map[$book['出借狀況']],
            ]);
        }
    }
```

執行 `behat` 應通過此 step 。

### 第三個 step ：書籍已被借出

建立一個 `CheckoutHistory` model ：

```bash
php artisan make:model CheckoutHistory -m
```

編輯 `app/CheckoutHistory.php` ，將內容修改如下：

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckoutHistory extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'returned',
    ];
}
```

編輯 `database/migrations/2015_09_01_175318_create_checkout_histories_table.php` ，在 schema 中加入 `book_id` 、 `user_id` 及 `returned` 三個欄位：

```php
Schema::create('checkout_histories', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('book_id');
    $table->integer('user_id');
    $table->boolean('returned')->default(false);
    $table->timestamps();
});
```

編輯 `database/factories/ModelFactory.php` ，加入以下內容：

```
$factory->define(App\CheckoutHistory::class, function (Faker\Generator $faker) {
    return [
        'book_id' => 'factory:' . App\Book::class,
        'user_id' => 'factory:' . App\User::class,
        'returned' => false,
    ];
});
```

編輯 `BookshelfContext::bookCheckedOutByUser` 方法，改成：

```php
    /**
     * @Given 書籍 :bookName 已被 :email 借出
     */
    public function bookCheckedOutByUser($bookName, $email)
    {
        $book = Book::where('name', $bookName)->first();
        $user = User::where('email', $email)->first();

        $book->available = false;
        $book->save();

        factory(CheckoutHistory::class)->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    }
```

並記得引用相關類別：

```php
use App\CheckoutHistory;
use App\User;
```

執行 `behat` 應通過此 step 。

## 書籍列表第一個 scenario ：使用者可查看書籍列表及出借狀況

### 第一個 step ：進入首頁

修改 `BookshelfContext::visitHome` ：

```php

    /**
     * @When 進入首頁
     */
    public function visitHome()
    {
        $this->visit('/');
    }
```

執行 `behat` 應通過。

### 第二個 step ：顯示書籍清單、出借狀況

瀏覽 `http://bookshelf.app/` ，利用開發者工具找出第一本書名的 CSS selector 。

```php
'html body div.container ul.list-group li.list-group-item.clearfix h3.pull-left'
```

去除共用的元素，留下：

```php
'ul.list-group li.list-group-item h3'
```

而因為 `li.list-group-item` 元素有多個，因此要用 `:nth-child` 來選擇：

```php
'ul.list-group li.list-group-item:nth-child(x) h3'
```

其中 `x` 就可以用 `TableNode` 的索引來組成，最後的元素如下：

```php
'ul.list-group li.list-group-item:nth-child(' . ($index + 1) . ') h3'
```

相同的方式找到「出借狀況」的 CSS selector ：

```php
'ul.list-group li.list-group-item:nth-child(' . ($index + 1) . ') span'
```

將 `BookshelfContext::booksList` 方法改成：

```php
public function booksList(TableNode $table)
{

    foreach ($table as $index => $book) {
        $this->assertElementContainsText('ul.list-group li.list-group-item:nth-child(' . ($index + 1) . ') h3', $book['書籍名稱']);
        $this->assertElementContainsText('ul.list-group li.list-group-item:nth-child(' . ($index + 1) . ') span', $book['出借狀況']);
    }
}
```

修改首頁路由：

```php
get('/', 'BookshelfController@index');
```

建立 `BookshelfController` 類別：

```bash
php artisan make:controller BookshelfController --plain
```

編輯 `app/Http/Controllers/BookshelfController.php` ，加入 `index` 方法：

```php
namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests;

class BookshelfController extends Controller
{
    public function index()
    {
        $books = Book::all();

        return view('bookshelf/index', compact('books'));
    }
}
```

修改 `resources/views/bookshelf/index.blade.php` ，將內容改成：

```html
@extends('layouts.app')

@section('content')

    <div class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/"><span>借書系統</span></a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-ex-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="/auth/logout">登出</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container">
        <ul class="list-group">

            @foreach($books as $book)
                <li class="list-group-item clearfix">
                    @if ($book->available)
                        <h3 class="pull-left">{{ $book->name }} <span class="badge alert-success">可借出</span></h3>
                        <button class="btn btn-info pull-right" type="submit">借書</button>
                    @else
                        <h3 class="pull-left">{{ $book->name }} <span class="badge alert-danger">已借出</span></h3>

                    @endif
                </li>
            @endforeach
        </ul>

        @include('partials.errors')

    </div> <!-- /container -->

@stop
```

執行 `behat` 應通過該 scenario 。

## 重構書籍列選取程式

在 `BookshelfContext::booksList` 方法中，提煉出 `getBookSelector` 方法：

```php
    protected function getBookSelector($index, $child = '')
    {
        return 'ul.list-group li.list-group-item:nth-child(' . $index . ') ' . $child;
    }
```

再將：

```php
$this->assertElementContainsText('ul.list-group li.list-group-item:nth-child(' . ($index + 1) . ') h3', $book['書籍名稱']);
$this->assertElementContainsText('ul.list-group li.list-group-item:nth-child(' . ($index + 1) . ') span', $book['出借狀況']);
```

改寫為：

```php
$this->assertElementContainsText($this->getBookSelector($index + 1, 'h3'), $book['書籍名稱']);
$this->assertElementContainsText($this->getBookSelector($index + 1, 'span'), $book['出借狀況']);
```

執行 `behat` 應通過。

## 書籍列表第二個 scenario ：使用者借書

在 `Bookshelf.feature` 中加入新的 scenario ：

```gherkin
    Scenario: 使用者借書
        Given 在列表的 "HTML5 + CSS3 專用網站設計"
        When 點選「借書」按鈕
        Then 出借狀況顯示 "已借出"
        And 顯示「還書」按鈕
```

接著建立對應的 step definitions 並重命名：

```php
    /**
     * @Given 在列表的 :bookName
     */
    public function selectBookOnShelf($bookName)
    {
        throw new PendingException();
    }

    /**
     * @When 點選「借書」按鈕
     */
    public function clickCheckoutButton()
    {
        throw new PendingException();
    }

    /**
     * @Then 出借狀況顯示 :statusText
     */
    public function expectedStatusText($statusText)
    {
        throw new PendingException();
    }

    /**
     * @Then 顯示「還書」按鈕
     */
    public function showReturnButton()
    {
        throw new PendingException();
    }
```

### 第一個 step ：在列表的書名

我們需要知道書名對應的表格列位置，方法如下：

1. 在建立書籍資料時，先記住它們的索引以及是否可借出。
2. 從書籍名稱找出對應的索引，利用該索引以及 `BookshelfContext::getBookSelector` 方法就可以找到該書籍所在的列元素。
3. 回到首頁，取得該書籍對應的列元素。 (回首頁是確保程式是在首頁動作)

在 `BookshelfContext` 類別中加入三個屬性：

```php
    /**
     * @var array
     */
    private $expectedBooks = [];

    /**
     * @var int
     */
    private $currentBookIndex = 0;

    /**
     * @var \Behat\Mink\Element\NodeElement
     */
    private $currentBookNode = null;
```

將 `BookshelfContext::onShelfBooks` 方法中的 `foreach` 迴圈改成：

```php
    foreach ($table as $index => $book) {
        factory(Book::class)->create([
            'name' => $book['書籍名稱'],
            'available' => $map[$book['出借狀況']],
        ]);

        $this->expectedBooks[$book['書籍名稱']] = [
            'index' => $index + 1,
            'available' => $map[$book['出借狀況']],
        ];
        $this->expectedBooks[$index + 1] = $book;
    }
```

建立一個 `getBookNode` 方法：

```php
    /**
     * @param $bookName
     * @return \Behat\Mink\Element\NodeElement
     */
    protected function getBookNode($bookName)
    {
        $index = $this->currentBookIndex = $this->expectedBooks[$bookName]['index'];
        $this->assertElementContainsText($this->getBookSelector($index, 'h3'), $bookName);
        return $this->getSession()
            ->getPage()
            ->find('css', $this->getBookSelector($index));
    }
```

最後完成 `selectBookOnShelf` 方法：

```php
    public function selectBookOnShelf($bookName)
    {
        $this->visitHome();
        $this->currentBookNode = $this->getBookNode($bookName);
    }
```

執行 `behat` 應通過此 step 。

### 第二個 step ：點選「借書」按鈕

將 `BookshelfContext::` 方法改為：

```php
    /**
     * @When 點選「借書」按鈕
     */
    public function clickCheckoutButton()
    {
        $button = $this->currentBookNode->findButton('借書');
        $button->click();
    }
```

從樣版著手，將：

```html
<button class="btn btn-info pull-right" type="submit">借書</button>
```

取代為：

```html
<form action="/bookshelf/checkout" method="post">
    {!! csrf_field() !!}
    <input type="hidden" name="book_id" value="{{ $book->id }}">
    <button class="btn btn-info pull-right" type="submit">
        借書
    </button>
</form>
```

在 `app/Http/routes.php` 中加入新路由：

```php
post('bookshelf/checkout', 'BookShelfController@checkout');
```

在 `BookshelfController` 類別中加入：

```php
    public function checkout(Request $request)
    {
        return redirect('/');
    }
```

目的是先讓 step 通過。

### 第三個 step ：出借狀況顯示 "已借出"

改寫 `BookshelfContext::expectedStatusText` 方法：

```php
    public function expectedStatusText($statusText)
    {
        $this->assertPageAddress('/');
        $index = $this->currentBookIndex;
        $this->assertElementContainsText($this->getBookSelector($index, 'span'), $statusText);
    }
```

在 `Book` 類別中加入：

```php
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkoutHistories()
    {
        return $this->hasMany(CheckoutHistory::class);
    }
```

重寫 `BookshelfController::checkout` 方法：

```php
    public function checkout(Request $request)
    {
        $bookId = $request->get('book_id');
        $book = Book::findOrFail($bookId);
        /** @var Book $book */
        $book->available = false;
        $book->save();
        $book->checkoutHistories()
            ->create([
                'user_id' => Auth::user()->id,
            ]);

        return redirect('/');
    }
```

執行 `behat` 應通過此 step 。

### 第四個 step ：顯示「還書」按鈕

改寫 `BookshelfContext::showReturnButton` 方法：

```php
    /**
     * @Then 顯示「還書」按鈕
     */
    public function showReturnButton()
    {
        $selector = $this->getBookSelector($this->currentBookIndex, 'button');
        $this->assertElementContainsText($selector, '還書');
    }
```

在 `resources/views/bookshelf/index.blade.php` 中的：

```html
<h3 class="pull-left">{{ $book->name }} <span class="badge alert-danger">已借出</span></h3>
```

底下加入：

```html
<button class="btn btn-warning pull-right" type="submit">還書</button>
```

執行 `behat` 應通過此 scenario 。

## 書籍列表第三個 scenario ：使用者還書

在 `Bookshelf.feature` 中加入：

```gherkin
    Scenario: 使用者還書
        Given 書籍 "PHP 聖經" 已被 "jaceju@example.com" 借出
        And 在列表的 "PHP 聖經"
        When 點選「還書」按鈕
        Then 出借狀況顯示 "可借出"
        And 顯示「借書」按鈕
```

建立 step definitions 並重命名：

```php

    /**
     * @When 點選「還書」按鈕
     */
    public function clickReturnButton()
    {
        throw new PendingException();
    }

    /**
     * @Then 顯示「借書」按鈕
     */
    public function showCheckoutButton()
    {
        throw new PendingException();
    }
```

### 第一個 step ：點選「還書」按鈕

將 `BookshelfContext::clickReturnButton` 改為：

```php
    public function clickReturnButton()
    {

        $button = $this->currentBookNode->findButton('還書');
        $button->click();
    }
```

將 `resources/views/bookshelf/index.blade.php` 中的：

```html
<button class="btn btn-warning pull-right" type="submit">還書</button>
```

改為：

```html
<form action="/bookshelf/return" method="post">
    {!! csrf_field() !!}
    <input type="hidden" name="book_id" value="{{ $book->id }}">
    <button class="btn btn-warning pull-right" type="submit">
        還書
    </button>
</form>
```

在 `app/Http/routes.php` 中加入：

```php
post('bookshelf/return', 'BookShelfController@returnBook');
```

在 `CheckoutHistory` 類別中加入：

```php
    public function scopeOfUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeNotReturned(Builder $query)
    {
        return $query->where('returned', false);
    }

    public function scopeNotReturnedByUser(Builder $query, $userId)
    {
        return $query->ofUser($userId)->notReturned();
    }
```

在 `BookshelfController` 類別中加入：

```php
    public function returnBook(Request $request)
    {
        $bookId = $request->get('book_id');
        $book = Book::findOrFail($bookId);
        /** @var Book $book */
        $book->available = true;
        $book->save();
        $checkHistory = $book->checkoutHistories()
            ->notReturnedByUser(Auth::user()->id)
            ->first();
        $checkHistory->returned = true;
        $checkHistory->save();

        return redirect('/');
    }
```

執行 `behat` 應通過此 step 。

### 第二個 step ：顯示「借書」按鈕

將 `BookshelfContext::showCheckoutButton` 方法改為：

```php
    public function showCheckoutButton()
    {
        throw new PendingException();
        $selector = $this->getBookSelector($this->currentBookIndex, 'button');
        $this->assertElementContainsText($selector, '借書');
    }
```

執行 `behat` 應通過此 scenario 。

## 書籍列表第四個 scenario ：使用者不能歸選別人借的書

在 `Bookshelf.feature` 中加入：

```gherkin
    Scenario: 使用者不能歸選別人借的書
        Given 在列表的 "專案管理實務"
        Then 不顯示「還書」按鈕
```

並在 `BookshelfContext` 類別中新增 step definition ：

```php
    /**
     * @Then 不顯示「還書」按鈕
     */
    public function shouldNotDisplayReturnButton()
    {
        $this->assertElementNotOnPage($this->getBookSelector($this->currentBookIndex, 'button'));
    }
```

將 `resources/views/bookshelf/index.blade.php` 中的：

```html
<button class="btn btn-warning pull-right" type="submit">還書</button>
```

改為：

```html
@if ($book->checkoutHistories()->ofUser(app('auth')->user()->id)->count() === 1)
<button class="btn btn-warning pull-right" type="submit">還書</button>
@endif
```

下一步：[重構](tips/07-refactoring.md)。