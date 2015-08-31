@extends('layouts.app')

@section('content')

    <div class="container">

        <form class="form-auth" method="POST" action="/auth/register">

            <h2 class="form-auth-heading">建立帳號</h2>

            @include('partials.errors')

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