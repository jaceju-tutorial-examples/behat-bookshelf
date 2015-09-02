<?php

use Behat\Behat\Tester\Exception\PendingException;
use Goez\BehatLaravelExtension\Context\LaravelContext;

class MembershipContext extends LaravelContext
{
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
}
