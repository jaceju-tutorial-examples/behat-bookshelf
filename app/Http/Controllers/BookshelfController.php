<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookshelfController extends Controller
{
    public function index()
    {
        $books = Book::all();

        return view('bookshelf/index', compact('books'));
    }

    public function checkout(Request $request)
    {
        $bookId = $request->get('book_id');
        $book = Book::findOrFail($bookId);
        /** @var Book $book */
        $book->available = false;
        $book->save();
        $book->checkoutHistories()
            ->create([
                'user_id' => Auth::user()->id,
            ]);

        return redirect('/');
    }

    public function returnBook(Request $request)
    {
        $bookId = $request->get('book_id');
        $book = Book::findOrFail($bookId);
        /** @var Book $book */
        $book->available = true;
        $book->save();
        $checkHistory = $book->checkoutHistories()
            ->notReturnedByUser(Auth::user()->id)
            ->first();
        $checkHistory->returned = true;
        $checkHistory->save();

        return redirect('/');
    }
}
