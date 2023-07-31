<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\BookStock;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Backpack\CRUD\app\Http\Controllers\Operations\Concerns\HasForm;

trait TransactionOperation
{
    use HasForm;

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupTransactionRoutes(string $segment, string $routeName, string $controller): void
    {
        $this->formRoutes(
            operationName: 'transaction',
            routesHaveIdSegment: true,
            segment: $segment,
            routeName: $routeName,
            controller: $controller
        );
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupTransactionDefaults(): void
    {
        $this->formDefaults(
            operationName: 'transaction',
            // buttonStack: 'line', // alternatives: top, bottom
            buttonMeta: [
                'icon' => 'la la-book',
                'label' => 'Loan This Book',
            ],
        );

        $this->crud->operation('transaction', function () {

            $currentEntry = $this->crud->getCurrentEntry();
            
            if(!$currentEntry->bookStock->count()) {
                $this->crud->denyAccess('transaction');
                abort(403, 'No Data');
            }
            
            $this->crud->field([
                'name'  => 'book_id',
                'type'  => 'hidden',
                'tab'   => 'Loan Form',
                'value' => $currentEntry->id,
            ]);
            $this->crud->field([
                'name'          => 'book_name',
                'label'         => 'Book Name',
                'type'          => 'text',
                'tab'           => 'Loan Form',
                'attributes'    => ['readonly' => 'readonly'],
                'value'         => $currentEntry->book_name,
            ]);
            $this->crud->field([
                'name'          => 'book_stock_id', // the relationship name in your Migration
                'label'         => 'Select Whick Book Location',
                'type'          => 'select',
                'entity'        => 'bookStock', // the relationship name in your Model
                'allows_null'   => false,
                'tab'           => 'Loan Form',
                'attribute'     => 'book_location_name',
                'options'       => (function($query) use($currentEntry){
                    return $currentEntry->bookStock->where('book_stock_qty','>',0);
                    // return $query->where('book_stock_qty','>',0)->get();
                }),
            ]);
            $this->crud->field([
                'name'          => 'member_id', // the relationship name in your Migration
                'type'          => 'select',
                'model'         => 'App\Models\Member', // the relationship name in your Model
                'allows_null'   => false,
                'tab'           => 'Loan Form',
                'attribute'     => 'member_name',
            ]);
            $this->crud->field([
                'name'          => 'transaction_book_qty',
                'type'          => 'number',
                'label'         => 'QTY to Loan',
                'tab'           => 'Loan Form',
                'default'       => 1,
                'attributes'    => ['min' => 1],
            ]);

            foreach ($currentEntry->bookStock as $key => $value) {
                $this->crud->field([
                    'name'          => 'book_name_'.$currentEntry->book_name.'_'.$key,
                    'label'         => $value->bookLocation->book_location_name,
                    'type'          => 'text',
                    'attributes'    => ['readonly' => 'readonly'],
                    'value'         => $value->book_stock_qty,
                    'tab'           => 'Book Current Stock',
                ]);
            }
            
        });
    }

    /**
     * Method to handle the GET request and display the View with a Backpack form
     *
     */
    public function getTransactionForm(?int $id = null) : \Illuminate\Contracts\View\View
    {
        $this->crud->hasAccessOrFail('transaction');

        return $this->formView($id);
    }

    /**
    * Method to handle the POST request and perform the operation
    *
    * @return array|\Illuminate\Http\RedirectResponse
    */
    public function postTransactionForm(?int $id = null)
    {
        $this->crud->hasAccessOrFail('transaction');

        return $this->formAction($id, function ($inputs, $entry) {
            
            if(!empty($inputs['book_id'])){
                // $findBookStock = BookStock::where('book_id',$inputs['book_id'])->where('book_location_id', $inputs['book_location_id'])->first();
                $findBookStock = BookStock::find($inputs['book_stock_id']);
                if(!empty($findBookStock)){
                    if($findBookStock->book_stock_qty - $inputs['transaction_book_qty'] >= 0){
                        if(Transaction::where('member_id',$inputs['member_id'])->where('transaction_returned_at',NULL)->first()){
                            \Alert::error('This member still loan 1 book and not return until now!')->flash();
                        }else {
                            DB::beginTransaction();
                            try {
                                $findBookStock->book_stock_qty = $findBookStock->book_stock_qty - $inputs['transaction_book_qty'];
    
                                if($findBookStock->save()){
                                    Transaction::create([
                                        'book_stock_id' => $findBookStock->id,
                                        'member_id' => $inputs['member_id'],
                                        'transaction_book_qty' => $inputs['transaction_book_qty'],
                                        'transaction_loaned_at' => date('Y-m-d'),
                                    ]);
                                }
                                DB::commit();
                                \Alert::success('Successfully loaned the book!')->flash();
                            } catch (\Throwable $th) {
                                DB::rollback();
                                \Alert::error($th->getMessage())->flash();
                            }
                        }
                        
                    }else {
                        \Alert::error('Check your book stock qty, your input more bigger than the actual stock')->flash();
                    }
                }else {
                    \Alert::error('Data not found!')->flash();
                }
            }else {
                \Alert::error('No Data')->flash();
            }

        });
    }
}
