<?php

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
}
