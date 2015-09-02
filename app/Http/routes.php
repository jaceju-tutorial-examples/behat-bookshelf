<?php

get('/', 'BookshelfController@index');

get('auth/register', 'Auth\AuthController@getRegister');
post('auth/register', 'Auth\AuthController@postRegister');

get('auth/login', 'Auth\AuthController@getLogin');
post('auth/login', 'Auth\AuthController@postLogin');

get('auth/logout', function () {
    return redirect('auth/login');
});
