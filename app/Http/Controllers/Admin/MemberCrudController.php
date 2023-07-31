<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MemberRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MemberCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MemberCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Member::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/member');
        CRUD::setEntityNameStrings('member', 'members');
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
            'name'      => 'member_profile_picture', // The db column name
            'type'      => 'image',
            'disk'   => 'public', 
            'height' => '130px',
            'width'  => '130px',
        ]);
        CRUD::addColumn([
            'name' => 'department_id', // the relationship name in your Migration
            'type' => 'select',
            'entity' => 'department', // the relationship name in your Model
            'attribute' => 'department_name',
        ]);
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
        CRUD::setValidation(MemberRequest::class);
        CRUD::addField([
            // 'name' => 'department_id', // the relationship name in your Migration
            // 'type' => 'select',
            // 'entity' => 'department', // the relationship name in your Model
            // 'allows_null' => false, // the relationship name in your Model
            // 'attribute' => 'department_name',

            'name' => 'department_id',
            'type' => 'livewire_select',
            'label' => 'Department Name',
            'hint' => 'Write more for best result',
            'attribute' => 'department_name',
            'default' => request()->has('department_id') ? request('department_id') : NULL,
            'model' => \App\Models\Department::class,
            'attributes' => [
                'autocomplete' => 'off'
            ],

        ]);

        CRUD::setFromDb(); // set fields from db columns.
        CRUD::field('member_profile_picture')
            ->type('upload')
            ->withFiles([
                'disk' => 'public', // the disk where file will be stored
                'path' => '/uploads/members/', // the path inside the disk where file will be stored
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
        CRUD::field('member_profile_picture')
            ->type('upload')
            ->withFiles([
                'disk' => 'public', // the disk where file will be stored
                'path' => '/uploads/members/', // the path inside the disk where file will be stored
        ]);
    }
}
