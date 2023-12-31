<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\BookStock;
use Illuminate\Support\Facades\DB;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\TransactionRequest;
use App\Models\Penalty;
use App\Models\Transaction;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TransactionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TransactionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Transaction::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/transaction');
        CRUD::setEntityNameStrings('transaction', 'transactions');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // dd(gettype(env('penaltyStatus')));
        CRUD::removeButtons(['create','delete','update','show']);
        CRUD::orderBy('transaction_returned_at','desc');
        if(request('filterShowAll')){
            Widget::add([
                'type'         => 'alert',
                'class'        => 'alert alert-success mb-2',
                'content'      => 'Ini adalah daftar semua peminjaman, baik yang sudah dikembalikan maupun sedang dipinjam.<br> Untuk melihat daftar peminjaman yang sedang berjalan saja silahkan klik tombol <strong>set ulang</strong> di bagian atas 👆',
                'close_button' => true, // show close button or not
            ]);
        }else{
            Widget::add([
                'type'         => 'alert',
                'class'        => 'alert alert-success mb-2',
                'content'      => 'Ini adalah daftar semua peminjaman yang belum dikembalikan <br> Untuk melihat semua peminjaman, silahkan klik <strong>Tampilkan semua 👇</strong>',
                'close_button' => true, // show close button or not
            ]);
            CRUD::addClause('where','transaction_returned_at',NULL);
            CRUD::addButtonFromModelFunction('top', 'filterShowAll', 'filterShowAll', 'end');
            CRUD::addButtonFromModelFunction('top', 'createTransactionBtn', 'createTransactionBtn', 'beginning');
        }
        

        CRUD::addColumn([
            "name" => "member_id",
            "type" => "select",
            "entity" => "member",
            "attribute" => "member_name",
            "limit" => 1000,
        ]);
        CRUD::addColumn([
            "name" => "penalty_id",
            "type" => "select",
            "entity" => "penalty",
            "attribute" => "penalty_cost",
            "limit" => 1000,
        ]);
        CRUD::addColumn([
            "name" => "book_stock_id",
            "label" => "Book Name",
            "type" => "select",
            "entity" => "bookStock.book",
            "attribute" => "book_name",
            "limit" => 1000,
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
        CRUD::setValidation(TransactionRequest::class);
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









    
    // Bulk transaction section
    protected function setupBulkBookReturnRoutes($segment, $routeName, $controller)
    {
        Route::post($segment.'/bulk-book-return', [
            'as'        => $routeName.'.bulkBookReturn',
            'uses'      => $controller.'@bulkBookReturn',
            'operation' => 'bulkBookReturn',
        ]);
    }

    public function bulkBookReturn()
    {
        $this->crud->hasAccessOrFail('list');
        
        $querySelectTransaction = $this->getSelectedTransaction($this->crud->getRequest()->input('entries'));

        // Cek jika data ada
        if(count($querySelectTransaction) < 1){
            return Response()->json([
                'error' => "the selected transaction entries already returned"
            ], 500); // Status code here
            
        }
        
        DB::beginTransaction();
        try {
            // update book stock here
            foreach ($querySelectTransaction as $key => $value) {
                
                // Set returned at in transaction table
                $value->update(['transaction_returned_at' => date('Y-m-d')]);

                // increase the book stock in book_stocks table
                BookStock::find($value->book_stock_id)->increment('book_stock_qty',$value->transaction_book_qty);

                // Check if this transaction have pelanty
                if(!empty(env('loanExpDays'))){
                    $loanedDate = Carbon::createFromFormat('Y-m-d', $value->transaction_loaned_at);
                    $threeDaysNext = $loanedDate->addDays(env('loanExpDays'))->format('Y-m-d');
                    if(date('Y-m-d') > $threeDaysNext){
                        // Calculate the difference between the two dates.
                        $now = date('Y-m-d');
                        $diff = Carbon::createFromFormat('Y-m-d', $threeDaysNext)->diff($now);
                        $penaltyCost = $diff->days * env('penaltyCost');
                        // dd($value->id, $now,$threeDaysNext, $diff->days,$penaltyCost);
                        Penalty::create([
                            'transaction_id' => $value->id,
                            'penalty_status' => 'unpaid',
                            'penalty_cost' => $penaltyCost,
                        ]);
                    }
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return Response()->json([
                'error' => $th->getMessage()
            ], 500); // Status code here
            
        }
    }

    protected function getSelectedTransaction($entries){
        return $this->crud->model->where('transaction_returned_at','=',null)->where(function($query) use($entries) {
            $query->whereIn('id', $entries);
        })->get();
    }




    protected function setupBulkBookReturnDefaults()
    {
        $this->crud->allowAccess('bulk_book_return');

        $this->crud->operation('list', function () {
            $this->crud->enableBulkActions();
            $this->crud->addButton('bottom', 'bulk_book_return', 'view', 'bulk_book_return', 'beginning');
        });
    }

}
