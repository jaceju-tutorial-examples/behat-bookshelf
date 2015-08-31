# 登入功能

## 登入功能第一個 scenario ：使用者登入系統，成功登入

建立 `features/membership/Authentication.feature` ，內容如下：

```gherkin
Feature: 使用者認證
    In order to 使用需要認證的系統
    As a 使用者
    I want to 輸入登錄資訊

    Scenario: 使用者登入系統，成功登入
        Given 帳號 "Jace Ju" "jaceju@example.com" 已註冊
        When 用帳號 "jaceju@example.com" 及密碼 "password" 登入系統
        Then 登入系統
        And 導向首頁
```

建立 step definition ：

```php
    /**
     * @When 用帳號 :email 及密碼 :password 登入系統
     */
    public function signIn($email, $password)
    {
        $this->visit('/auth/login');
        $this->fillField('email', $email);
        $this->fillField('password', $password);
        $this->pressButton('登入');
    }
```

在 `app/Http/routes.php` 中修改 `auth/login` 相關路由：

```php
get('auth/login', 'Auth\AuthController@getLogin');
post('auth/login', 'Auth\AuthController@postLogin');
```

修改 `resources/views/auth/login.blade.php` ，加入 `@include('partials.errors')` 、 `{!! csrf_field() !!}` 與 ` value="{{ old('email') }}"` ：

```html
@extends('layouts.app')

@section('content')

    <div class="container">

        <form class="form-auth" method="POST" action="/auth/login">
            <h2 class="form-auth-heading">登入</h2>

            @include('partials.errors')

            {!! csrf_field() !!}

            <!-- Email -->
            <label for="input-email" class="sr-only">Email</label>
            <input type="text" name="email" id="input-email" class="form-control input-top" placeholder="Email" value="{{ old('email') }}" autofocus>

            <!-- Password -->
            <label for="input-password" class="sr-only">密碼</label>
            <input type="password" name="password" id="input-password" class="form-control input-bottom" placeholder="密碼">

            <!-- Link and Button -->
            <p><a href="/auth/register">建立帳號</a></p>
            <button class="btn btn-lg btn-primary btn-block" type="submit">登入</button>

        </form>

    </div> <!-- /container -->

@stop
```

執行 `behat` 查看結果。

## 登入功能第二個 scenario ：使用者登入系統，輸入密碼錯誤

在 `Authentication.feature` 中新增 scenario ：

```gherkin
    Scenario: 使用者登入系統，輸入密碼錯誤
        Given 帳號 "Jace Ju" "jaceju@example.com" 已註冊
        When 用帳號 "jaceju@example.com" 及密碼 "PASSWORD" 登入系統
        Then 頁面出現錯誤訊息 "登入失敗，帳號或密碼錯誤"
```

因為在 `Illuminate\Foundation\Auth\AuthenticatesUsers` 這個 trait 的 `postLogin` 方法中有定義 `getFailedLoginMessage` 方法，所以可以在 `AuthController` 類別中覆寫它：

```php
    protected function getFailedLoginMessage()
    {
        return "登入失敗，帳號或密碼錯誤";
    }
```

另一個方法是加入 `resources/lang/zh-tw/auth.php` ，不過這邊採取簡單實作。

執行 `behat` ，應該通過。

## 登入功能第三個 scenario ：使用者登入系統，帳號不存在

在 `Authentication.feature` 中新增 scenario ：

```gherkin
    Scenario: 使用者登入系統，帳號不存在
        When 用帳號 "jaceju@example.com" 及密碼 "password" 登入系統
        Then 頁面出現錯誤訊息 "登入失敗，帳號或密碼錯誤"
```

執行 `behat` ，應該通過。

下一步：[書籍借閱功能](tips/06-bookshelf.md)。
