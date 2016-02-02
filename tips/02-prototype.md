# 建立 prototype

主要 layout ，建立 `resources/views/layouts/app.blade.php` ：

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Laravel</title>
    <!-- Font and Styles -->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Lato:100">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@yield('content')

<!-- Scripts -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
```

註冊畫面，建立 `resources/views/auth/register.blade.php` ：

```html
@extends('layouts/app')

@section('content')

    <div class="container">

        <form class="form-auth" method="POST" action="/auth/register">

            <h2 class="form-auth-heading">建立帳號</h2>

            <!-- Name -->
            <label for="input-name" class="sr-only">姓名</label>
            <input type="text" name="name" id="input-name" class="form-control input-top" placeholder="姓名" value="" autofocus>

            <!-- Email -->
            <label for="input-email" class="sr-only">Email</label>
            <input type="text" name="email" id="input-email" class="form-control input-top" placeholder="Email" value="" autofocus>

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

登入畫面，建立 `resources/views/auth/login.blade.php` ：

```html
@extends('layouts/app')

@section('content')

    <div class="container">

        <form class="form-auth" method="POST" action="/auth/login">
            <h2 class="form-auth-heading">登入</h2>

            <!-- Email -->
            <label for="input-email" class="sr-only">Email</label>
            <input type="text" name="email" id="input-email" class="form-control input-top" placeholder="Email" autofocus>

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

書籍列表畫面，建立 `resources/views/bookshelf/index.blade.php` ：

```html
@extends('layouts/app')

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
            <li class="list-group-item clearfix">
                <h3 class="pull-left">專案管理實務 <span class="badge alert-success">可借出</span></h3>
                <button class="btn btn-info pull-right" type="submit">
                    借書
                </button>
            </li>
            <li class="list-group-item clearfix">
                <h3 class="pull-left">HTML5 + CSS3 專用網站設計 <span class="badge alert-success">可借出</span></h3>
                <button class="btn btn-info pull-right" type="submit">借書</button>
            </li>
            <li class="list-group-item clearfix">
                <h3 class="pull-left">JavaScript 學習手冊 <span class="badge alert-success">可借出</span></h3>

                <button class="btn btn-info pull-right" type="submit">借書</button>
            </li>
            <li class="list-group-item clearfix">
                <h3 class="pull-left">精通 VI <span class="badge alert-success">可借出</span></h3>
                <button class="btn btn-info pull-right" type="submit">借書</button>
            </li>
            <li class="list-group-item clearfix">
                <h3 class="pull-left">PHP 聖經 <span class="badge alert-success">可借出</span></h3>
                <button class="btn btn-info pull-right" type="submit">借書</button>
            </li>
        </ul>
    </div> <!-- /container -->

@stop
```

設定路由，編輯 `app/Http/routes.php` ：

```php
get('/', function () {
    return view('bookshelf/index');
});

get('auth/register', function () {
    return view('auth/register');
});

get('auth/login', function () {
    return view('auth/login');
});

get('auth/logout', function () {
    return redirect('auth/login');
});
```

設定樣式，建立 `public/css/app.css` ：

```css
@charset "utf-8";

body {
  padding-bottom: 40px;
  background-color: #eee;
}

.form-auth {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-auth .form-auth-heading,
.form-auth .checkbox {
  margin-bottom: 10px;
}
.form-auth .checkbox {
  font-weight: normal;
}
.form-auth .form-control {
  position: relative;
  height: auto;
  box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form-auth .form-control:focus {
  z-index: 2;
}
.form-auth .input-top {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-auth .input-middle {
  margin-bottom: -1px;
  border-radius: 0;
}
.form-auth .input-bottom {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
```

透過 `artisan` 啟動 PHP 內建的 Web Server ：

```bash
php artisan serve
```

用瀏覽器連上 `http://localhost:8000` ，看看畫面是否有如預期。

下一步：[設定專案](./03-setup.md)。
