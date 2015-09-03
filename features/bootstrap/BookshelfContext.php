<?php

use Behat\Behat\Tester\Exception\PendingException;
use App\Book;
use App\CheckoutHistory;
use App\User;
use Behat\Gherkin\Node\TableNode;
use Goez\BehatLaravelExtension\Context\LaravelContext;

class BookshelfContext extends LaravelContext
{
    use Authentication;

    /**
     * @var array
     */
    private $expectedBooks = [];

    /**
     * @var int
     */
    private $currentBookIndex = 0;

    /**
     * @var \Behat\Mink\Element\NodeElement
     */
    private $currentBookNode = null;

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

        foreach ($table as $index => $book) {
            factory(Book::class)->create([
                'name' => $book['書籍名稱'],
                'available' => $map[$book['出借狀況']],
            ]);

            $this->expectedBooks[$book['書籍名稱']] = [
                'index' => $index + 1,
                'available' => $map[$book['出借狀況']],
            ];
            $this->expectedBooks[$index + 1] = $book;
        }
    }

    /**
     * @Given 書籍 :bookName 已被 :email 借出
     */
    public function bookCheckedOutByUser($bookName, $email)
    {
        $book = Book::where('name', $bookName)->first();
        $user = User::where('email', $email)->first();

        $book->available = false;
        $book->save();

        factory(CheckoutHistory::class)->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @When 進入首頁
     */
    public function visitHome()
    {
        $this->visit('/');
    }

    protected function getBookSelector($index, $child = '')
    {
        return 'ul.list-group li.list-group-item:nth-child(' . $index . ') ' . $child;
    }

    /**
     * @Then 顯示書籍清單、出借狀況
     */
    public function booksList(TableNode $table)
    {
        foreach ($table as $index => $book) {
            $this->assertElementContainsText($this->getBookSelector($index + 1, 'h3'), $book['書籍名稱']);
            $this->assertElementContainsText($this->getBookSelector($index + 1, 'span'), $book['出借狀況']);
        }
    }

    /**
     * @param $bookName
     * @return \Behat\Mink\Element\NodeElement
     */
    protected function getBookNode($bookName)
    {
        $index = $this->currentBookIndex = $this->expectedBooks[$bookName]['index'];
        $this->assertElementContainsText($this->getBookSelector($index, 'h3'), $bookName);
        return $this->getSession()
            ->getPage()
            ->find('css', $this->getBookSelector($index));
    }

    /**
     * @Given 在列表的 :bookName
     */
    public function selectBookOnShelf($bookName)
    {
        $this->visitHome();
        $this->currentBookNode = $this->getBookNode($bookName);
    }

    /**
     * @When 點選「借書」按鈕
     */
    public function clickCheckoutButton()
    {
        $button = $this->currentBookNode->findButton('借書');
        $button->click(); // 一定要可以 submit 的 input 或 button
    }

    /**
     * @Then 出借狀況顯示 :statusText
     */
    public function expectedStatusText($statusText)
    {
        $this->assertPageAddress('/');
        $index = $this->currentBookIndex;
        $this->assertElementContainsText($this->getBookSelector($index, 'span'), $statusText);
    }

    /**
     * @Then 顯示「還書」按鈕
     */
    public function showReturnButton()
    {
        $selector = $this->getBookSelector($this->currentBookIndex, 'button');
        $this->assertElementContainsText($selector, '還書');
    }

    /**
     * @When 點選「還書」按鈕
     */
    public function clickReturnButton()
    {
        $button = $this->currentBookNode->findButton('還書');
        $button->click(); // 一定要可以 submit 的 input 或 button
    }

    /**
     * @Then 顯示「借書」按鈕
     */
    public function showCheckoutButton()
    {
        $selector = $this->getBookSelector($this->currentBookIndex, 'button');
        $this->assertElementContainsText($selector, '借書');
    }
}
