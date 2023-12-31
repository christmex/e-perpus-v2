<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('author', 'AuthorCrudController');
    Route::crud('book-type', 'BookTypeCrudController');
    Route::crud('publisher', 'PublisherCrudController');
    Route::crud('book-location', 'BookLocationCrudController');
    Route::crud('department', 'DepartmentCrudController');
    Route::crud('member', 'MemberCrudController');
    Route::crud('book', 'BookCrudController');
    Route::crud('book-stock', 'BookStockCrudController');
    Route::crud('transaction', 'TransactionCrudController');
    Route::crud('penalty-status', 'PenaltyStatusCrudController');
    Route::crud('penalty', 'PenaltyCrudController');
    // Route::get('loan_book', 'LoanBookController@index')->name('page.loan_book.index');
    Route::crud('loan-book', 'LoanBookController');
}); // this should be the absolute last line of this file