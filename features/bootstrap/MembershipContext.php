<?php

use Goez\BehatLaravelExtension\Context\LaravelContext;

class MembershipContext extends LaravelContext
{
    use Authentication;

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
     * @Then 導向首頁
     */
    public function iBeRedirectedHome()
    {
        $this->assertHomepage();
    }

    /**
     * @Then 頁面出現錯誤訊息 :message
     */
    public function assertPageContainsErrorMessage($message)
    {
        $this->assertPageContainsText($message);
    }

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
}
