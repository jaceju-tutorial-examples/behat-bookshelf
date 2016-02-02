# 註冊功能

## 註冊功能第一個 scenario ：使用者註冊帳號成功

建立 `features/membership/Registration.feature` 檔，內容如下：

```gherkin
Feature: 使用者可以註冊帳號
    In order to 使用需要認證的系統
    As a 訪客
    I want to 註冊帳號

    Scenario: 使用者註冊帳號成功
        When 註冊帳號 "Jace Ju" "jaceju@example.com"
        Then 登入系統
        And 導向首頁
```

用以下指令在 `MembershipContext` 類別中產生對應的 step definitions ：

```bash
./vendor/bin/behat --append-snippets
```

產生好的 step definiions 如下，但是方法名稱是很怪的拼音字：

```php

    /**
     * @When 註冊帳號 :arg1 :arg2
     */
    public function angShen($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then 登入系統
     */
    public function fouXuLian()
    {
        throw new PendingException();
    }

    /**
     * @Then 導向首頁
     */
    public function jieXuan()
    {
        throw new PendingException();
    }
```

重新將它們命名為較易懂的英文名稱，但要小心不要改動到中文註解的部份：

```php
    /**
     * @When 註冊帳號 :name :email
     */
    public function iRegisterAccount($name, $email)
    {
        throw new PendingException();
    }

    /**
     * @Then 登入系統
     */
    public function iHaveLoggedIn()
    {
        throw new PendingException();
    }

    /**
     * @Then 導向首頁
     */
    public function iBeRedirectedHome()
    {
        throw new PendingException();
    }
```

### 第一個 step ：註冊帳號

依照測試的方式，填寫 `MembershipContext::iRegisterAccount` 方法的內容：

```php
    /**
     * @When 註冊帳號 :name :email
     */
    public function iRegisterAccount($name, $email)
    {
        $this->visit('/auth/register');
        $this->fillField('name', $name);
        $this->fillField('email', $email);
        $this->fillField('password', 'password');
        $this->fillField('password_confirmation', 'password');

        $this->pressButton('註冊');
    }
```

目標是通過這個 step ，所以在 `app/Http/routes.php` 中，把 `auth/register` 改成：

```php
get('auth/register', 'Auth\AuthController@getRegister');
```

因為 Laravel 已經事先寫好 `Auth\AuthController` 了，所以可以直接執行 `behat` 來看結果。

### 第二個 step ：登入系統

在 `MembershipContext` 類別中引用 `Illuminate\Support\Facades\Auth` ：

```php
use Illuminate\Support\Facades\Auth;
```

修改 `MembershipContext::iHaveLoggedIn` 方法：

```php
    /**
     * @Then 登入系統
     */
    public function iHaveLoggedIn()
    {
        $this->assertTrue(Auth::check());
    }
```

接著修改 `resources/views/auth/register.blade.php` ，加入 `{!! csrf_field() !!}` 及 `{{ old(...) }}` ：

```html
@extends('layouts.app')

@section('content')
    <div class="container">

        <form class="form-auth" method="POST" action="/auth/register">

            <h2 class="form-auth-heading">建立帳號</h2>

            {!! csrf_field() !!}

            <!-- Name -->
            <label for="input-name" class="sr-only">姓名</label>
            <input type="text" name="name" id="input-name" class="form-control input-top" placeholder="姓名" value="{{ old('name') }}" autofocus>

            <!-- Email -->
            <label for="input-email" class="sr-only">Email</label>
            <input type="text" name="email" id="input-email" class="form-control input-top" placeholder="Email" value="{{ old('email') }}" autofocus>

            <!-- Passowrd -->
            <label for="input-password" class="sr-only">密碼</label>
            <input type="password" name="password" id="input-password" class="form-control input-middle" placeholder="密碼">

            <!-- Confirm Passowrd -->
            <label for="input-password" class="sr-only">確認密碼</label>
            <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control input-bottom" placeholder="確認密碼">

            <!-- Link and Button -->
            <p><a href="/auth/login">已經有帳號</a></p>
            <button class="btn btn-lg btn-primary btn-block" type="submit">註冊</button>
        </form>
    </div>

@stop
```

最後在 `app/Http/routes.php` 加入新的路由：

```php
post('auth/register', 'Auth\AuthController@postRegister');
```

執行 `behat` 來看結果。

### 第三個 step ：導向首頁

修改 `MembershipContext::iBeRedirectedHome` 方法：

```php
    /**
     * @Then 導向首頁
     */
    public function iBeRedirectedHome()
    {
        $this->assertHomepage();
    }
```

執行 `behat` 會出現以下錯誤：

```
Current page is "/home", but "/" expected. (Behat\Mink\Exception\ExpectationException)
```

這是因為預設登入後會導向 `/home` 。

需要在 `Auth\AuthController` 類別中加入 `$redirectTo` 屬性：

```php
    protected $redirectTo = '/';
```

執行 `behat` 後應該就會通過第一個 scenario 。

## 註冊功能第二個 scenario ：使用者註冊未輸入帳號及密碼

```gherkin
    Scenario: 使用者註冊未輸入帳號及密碼
        When 註冊帳號 "" ""
        Then 頁面出現錯誤訊息 "請輸入帳號與密碼"
```

執行：

```bash
./vendor/bin/behat --append-snippets
```

會在 `MembershipContext` 類別中多出以下方法：

```php
    /**
     * @Then 頁面出現錯誤訊息 :arg1
     */
    public function xuanXiaoBiLeiHaoHao($arg1)
    {
        throw new PendingException();
    }
```

將它修改為：

```php

    /**
     * @Then 頁面出現錯誤訊息 :message
     */
    public function assertPageContainsErrorMessage($message)
    {
        $this->assertPageContainsText($message);
    }
```

新增 `resources/views/partials/errors.blade.php` ，內容如下：

```html
@if (isset($errors) && count($errors) > 0)
    <div class="alert alert-danger">{{ $errors->first(null, ':message') }}</div>
@endif
```

在 `resources/views/auth/register.blade.php` 的 `<h2 ...>Create account</h2>` 該行下方加入：

```php
@include('partials.errors')
```

執行 `behat` ，會得到以下錯誤：

```
The text "請輸入帳號與密碼" was not found anywhere in the text of the current page. (Behat\Mink\Exception\ResponseTextException)
```

將 `Auth\AuthController` 類別中的 `validator` 方法改成 (在 `Validator::make` 方法中加入第三個參數) ：

```php
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ], [
            'required' => '請輸入帳號與密碼',
        ]);
    }
```

執行 `behat` ，應通過。

## 註冊功能第三個 scenario ：使用者註冊已存在的帳號

加入註冊的最後一個 scenario ，這次多了 `Background` ：

```gherkin
    Background:
        Given 帳號 "Taylor Otwell" "taylorotwell@example.com" 已註冊

    Scenario: 使用者註冊已存在的帳號
        When 註冊帳號 "Taylor Otwell" "taylorotwell@example.com"
        Then 頁面出現錯誤訊息 "您所輸入的帳號已經有人申請"
```

執行 `behat --append-snippets` 建立新的 step definition 後，直接改名並填入內容：

```php
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
```

其中密碼預設用 `password` 。

編輯 `AuthController::validator` 方法，在 `Validator::make` 方法的第三個參數陣列加入：

```php
'unique' => '您所輸入的帳號已經有人申請',
```

執行 `behat` 查看結果。

下一步：[登入功能](./05-login.md)。
