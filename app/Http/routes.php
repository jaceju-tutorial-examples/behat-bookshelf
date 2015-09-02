<?php

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
