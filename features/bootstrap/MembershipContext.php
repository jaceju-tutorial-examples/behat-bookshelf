<?php

use Behat\Behat\Tester\Exception\PendingException;
use Goez\BehatLaravelExtension\Context\LaravelContext;
use Illuminate\Support\Facades\Auth;

class MembershipContext extends LaravelContext
{
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

    /**
     * @Then 登入系統
     */
    public function iHaveLoggedIn()
    {
        $this->assertTrue(Auth::check());
    }

    /**
     * @Then 導向首頁
     */
    public function iBeRedirectedHome()
    {
        throw new PendingException();
    }
}
