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