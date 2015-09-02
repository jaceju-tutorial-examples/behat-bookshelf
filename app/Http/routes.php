<?php

get('/', function () {
    return view('bookshelf/index');
});

get('auth/register', 'Auth\AuthController@getRegister');
post('auth/register', 'Auth\AuthController@postRegister');

get('auth/login', function () {
    return view('auth/login');
});

get('auth/logout', function () {
    return redirect('auth/login');
});
