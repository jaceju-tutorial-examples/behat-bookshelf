<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests;

class BookshelfController extends Controller
{
    public function index()
    {
        $books = Book::all();

        return view('bookshelf/index', compact('books'));
    }
}
