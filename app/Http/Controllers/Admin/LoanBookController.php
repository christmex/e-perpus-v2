<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LoanBookController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LoanBookController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \App\Http\Controllers\Admin\Operations\TransactionOperation;
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Book::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/loan-book');
        CRUD::setEntityNameStrings('loan book', 'Loan Book');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name'      => 'book_cover', // The db column name
            'type'      => 'image',
            'disk'   => 'public', 
            'height' => '130px',
            'width'  => '130px',
        ]);
        CRUD::column('book_name');
        CRUD::column('all_book_stock')->type('number');
        CRUD::addColumn([
            "label" => "Loaned By",
            "type" => "select",
            "name" => "loaned_by",
            "entity" => "transactions.member",
            "attribute" => "member_name",
            "limit" => 1000,
        ]);
        CRUD::addColumn([
            "label" => "Stock Left",
            "type" => "select",
            "name" => "stock_left",
            "entity" => "bookStock",
            "attribute" => "book_stock_qty",
            "limit" => 1000,
        ]);
    }

    // public function index()
    // {
    //     return view('admin.loan_book', [
    //         'title' => 'Loan Book',
    //         'breadcrumbs' => [
    //             trans('backpack::crud.admin') => backpack_url('dashboard'),
    //             'LoanBook' => false,
    //         ],
    //         'page' => 'resources/views/admin/loan_book.blade.php',
    //         'controller' => 'app/Http/Controllers/Admin/LoanBookController.php',
    //     ]);
    // }
}
