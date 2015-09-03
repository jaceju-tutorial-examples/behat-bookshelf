<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;

class BookshelfController extends Controller
{
    /**
     * @var Authenticatable
     */
    private $user;

    public function __construct(Authenticatable $user)
    {
        $this->middleware('auth');
        $this->user = $user;
    }

    public function index()
    {
        $books = $this->getAllBooks();
        return view('bookshelf/index', compact('books'));
    }

    public function checkout(Request $request)
    {
        $bookId = $request->get('book_id');
        $this->checkoutBookById($bookId);
        return redirect('/');
    }

    public function returnBook(Request $request)
    {
        $bookId = $request->get('book_id');
        $this->returnBookById($bookId);
        return redirect('/');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBooks()
    {
        $books = Book::all();
        return $books;
    }

    /**
     * @param $bookId
     */
    public function checkoutBookById($bookId)
    {
        $book = Book::findOrFail($bookId);
        /** @var Book $book */
        $book->available = false;
        $book->save();
        $book->checkoutHistories()
            ->create([
                'user_id' => $this->user->id,
            ]);
    }

    /**
     * @param $bookId
     */
    public function returnBookById($bookId)
    {
        $book = Book::findOrFail($bookId);
        /** @var Book $book */
        $book->available = true;
        $book->save();
        $checkHistory = $book->checkoutHistories()
            ->notReturnedByUser($this->user->id)
            ->first();
        $checkHistory->returned = true;
        $checkHistory->save();
    }
}
