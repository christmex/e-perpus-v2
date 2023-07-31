<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BookRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BookCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BookCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \App\Http\Controllers\Admin\Operations\AddBookStockOperation;
    use \App\Http\Controllers\Admin\Operations\RemoveBookStockOperation;
    use \App\Http\Controllers\Admin\Operations\TransactionOperation;
    use \App\Http\Controllers\Admin\Operations\PrintBookLabelOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Book::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/book');
        CRUD::setEntityNameStrings('book', 'books');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name'      => 'book_cover', // The db column name
            'type'      => 'image',
            'disk'   => 'public', 
            'height' => '130px',
            'width'  => '130px',
        ]);
        CRUD::setFromDb(); // set columns from db columns.
        CRUD::column('book_publish_year')->type('text');
        CRUD::column('all_book_stock')->type('number');
        CRUD::addColumn([
            "label" => "Stock Left",
            "type" => "select",
            "name" => "stock_left",
            "entity" => "bookStock",
            "attribute" => "book_stock_qty",
            "limit" => 1000,
        ]);
        CRUD::addColumn([
            "label" => "Book Location",
            "type" => "select",
            "name" => "book_location",
            "entity" => "bookStock",
            "attribute" => "book_location_name",
            "limit" => 1000,
        ]);
        CRUD::addColumn([
            "label" => "Loaned By",
            "type" => "select",
            "name" => "loaned_by",
            "entity" => "transactions.member",
            "attribute" => "member_name",
            "limit" => 1000,
        ]);

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        
        CRUD::setValidation(BookRequest::class);
        CRUD::setFromDb(); // set fields from db columns.
        CRUD::field('book_publish_year')->type('number')->attributes(['min' => 0]);
        CRUD::field('book_cover')
            ->type('upload')
            ->withFiles([
                'disk' => 'public', // the disk where file will be stored
                'path' => '/uploads/books/', // the path inside the disk where file will be stored
        ]);
        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function setupDeleteOperation()
    {
        CRUD::field('book_cover')
            ->type('upload')
            ->withFiles([
                'disk' => 'public', // the disk where file will be stored
                'path' => '/uploads/books/', // the path inside the disk where file will be stored
        ]);
    }
}
