<?php

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