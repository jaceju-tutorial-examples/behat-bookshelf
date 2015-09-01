<?php

use App\Book;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Goez\BehatLaravelExtension\Context\LaravelContext;

class BookshelfContext extends LaravelContext
{
    use Authentication;

    /**
     * @Given 用帳號 :name :email 登入系統
     */
    public function iHaveLoggedInAs($name, $email)
    {
        $this->registeredAccount($name, $email);
        $this->signInAs($email);
        $this->iHaveLoggedIn();
    }

    /**
     * @Given 書架上現有書籍
     */
    public function onShelfBooks(TableNode $table)
    {
        $map = [
            '可借出' => true,
            '已借出' => false,
        ];

        foreach ($table as $book) {
            factory(Book::class)->create([
                'name' => $book['書籍名稱'],
                'available' => $map[$book['出借狀況']],
            ]);
        }
    }

    /**
     * @Given 書籍 :bookName 已被 :email 借出
     */
    public function bookCheckedOutByUser($bookName, $email)
    {
        throw new PendingException();
    }

    /**
     * @When 進入首頁
     */
    public function visitHome()
    {
        throw new PendingException();
    }

    /**
     * @Then 顯示書籍清單、出借狀況
     */
    public function booksList(TableNode $table)
    {
        throw new PendingException();
    }
}
