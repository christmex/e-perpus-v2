<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PenaltyRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PenaltyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PenaltyCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Penalty::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/penalty');
        CRUD::setEntityNameStrings('penalty', 'penalties');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::removeButtons(['create','show','delete']);
        CRUD::addColumn([
            "name" => "member",
            "type" => "select",
            "entity" => "transaction.member",
            "attribute" => "member_name",
            "limit" => 1000,
        ]);
        CRUD::addColumn([
            "name" => "member_department",
            "type" => "select",
            "entity" => "transaction.member",
            "attribute" => "department_name",
            "limit" => 1000,
        ]);
        // CRUD::addColumn([
        //     'name'          => 'book', // the relationship name in your Migration
        //     'type'          => 'select',
        //     'entity'         => 'book_name', // the relationship name in your Model
        //     'attribute'     => 'book_name',
        // ]);
        CRUD::addColumn([
            "name" => "transaction_id",
            "label" => "Book Name",
            "type" => "select",
            "entity" => "transaction.bookStock",
            "attribute" => "book_name",
            "limit" => 1000,
        ]);
        // CRUD::addColumn([
        //     "name" => "book",
        //     "type" => "select",
        //     "entity" => "transaction",
        //     "attribute" => "book_name",
        //     "limit" => 1000,
        // ]);
        CRUD::setFromDb(); // set columns from db columns.

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
        CRUD::setValidation(PenaltyRequest::class);

        $string = env('penaltyStatus');
        // Remove the single quotes and convert it to a valid PHP array format
        $validArrayString = str_replace(['[', ']', '=>'], ['[', ']', '=>'], $string);
        // Now, convert the valid PHP array string to an actual array
        $array = eval("return $validArrayString;");

        CRUD::addField([
            'name' => 'penalty_status', // the relationship name in your Migration
            'type' => 'select_from_array',
            'options'     => $array,
            'allows_null' => false,
        ]);
        CRUD::field('penalty_cost')->attributes(['disabled' => 'disabled']);
        CRUD::field('transaction_id')->type('hidden');
        CRUD::setFromDb(); // set fields from db columns.

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
}
