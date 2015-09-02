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
        return redirect('/');
    }
}
