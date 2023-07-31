<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\BookStock;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\BookStockRequest;
use Backpack\CRUD\app\Http\Controllers\Operations\Concerns\HasForm;

trait RemoveBookStockOperation
{
    use HasForm;

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupRemoveBookStockRoutes(string $segment, string $routeName, string $controller): void
    {
        $this->formRoutes(
            operationName: 'removeBookStock',
            routesHaveIdSegment: true,
            segment: $segment,
            routeName: $routeName,
            controller: $controller
        );
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupRemoveBookStockDefaults(): void
    {

        $this->formDefaults(
            operationName: 'removeBookStock',
            buttonStack: 'line', // alternatives: top, bottom
            buttonMeta: [
                'icon' => 'la la-minus',
                'label' => 'Remove Book Stock',
            ],
        );
        $this->crud->operation('removeBookStock', function () {

            $currentEntry = $this->crud->getCurrentEntry();
            
            if(!$currentEntry->bookStock->count()) {
                $this->crud->denyAccess('removeBookStock');
                abort(403, 'No Data');
            }

            // Add the request validation here
            $this->crud->setValidation(BookStockRequest::class);

            
            // We dont need this anymore, but if in the future there is a problem, uncomment this, and change all $entry->id to $inputs['book_id']
            // $this->crud->field([
            //     'name'  => 'book_id',
            //     'type'  => 'hidden',
            //     'tab'   => 'Form Remove Stock',
            //     'value' => $currentEntry->id,
            // ]);
            $this->crud->field([
                'name'          => 'book_name',
                'label'         => 'Book Name',
                'type'          => 'text',
                'tab'           => 'Form Remove Stock',
                'attributes'    => ['readonly' => 'readonly'],
                'value'         => $currentEntry->book_name,
            ]);
            $this->crud->field([
                'name'          => 'book_location_id', // the relationship name in your Migration
                'type'          => 'select',
                'entity'        => 'bookStock', // the relationship name in your Model
                'allows_null'   => false,
                'tab'           => 'Form Remove Stock',
                'attribute'     => 'book_location_name',
                'options'       => (function($query) use($currentEntry){
                    return $currentEntry->bookStock->where('book_stock_qty','>',0);
                    // return $query->where('book_stock_qty','>',0)->get();
                }),
            ]);
            $this->crud->field([
                'name'          => 'book_stock_qty',
                'type'          => 'number',
                'tab'           => 'Form Remove Stock',
                'attributes'    => ['min' => 1],
            ]);
            $this->crud->field([
                'name'          => 'remove_book_description',
                'type'          => 'textarea',
                'tab'           => 'Form Remove Stock',
            ]);

            foreach ($currentEntry->bookStock as $key => $value) {
                $this->crud->field([
                    'name'          => 'book_name_'.$currentEntry->book_name.'_'.$key,
                    'label'         => $value->bookLocation->book_location_name,
                    'type'          => 'text',
                    'attributes'    => ['readonly' => 'readonly'],
                    'value'         => $value->book_stock_qty,
                    'tab'          => 'Book Previous Stock',
                ]);
            }
            
        });
    }

    /**
     * Method to handle the GET request and display the View with a Backpack form
     *
     */
    public function getRemoveBookStockForm(?int $id = null) : \Illuminate\Contracts\View\View
    {
        $this->crud->hasAccessOrFail('removeBookStock');

        return $this->formView($id);
    }

    /**
    * Method to handle the POST request and perform the operation
    *
    * @return array|\Illuminate\Http\RedirectResponse
    */
    public function postRemoveBookStockForm(?int $id = null)
    {
        $this->crud->hasAccessOrFail('removeBookStock');

        return $this->formAction($id, function ($inputs, $entry) {
            // You logic goes here...
            // dd('got to ' . __METHOD__, $inputs, $entry);
            if(!empty($entry->id)){
                // $findBookStock = BookStock::where('book_id',$entry->id)->where('book_location_id', $inputs['book_location_id'])->first();
                $findBookStock = BookStock::find($inputs['book_location_id']);
                if(!empty($findBookStock)){
                    DB::beginTransaction();
                    try {
                        if($findBookStock->book_stock_qty - $inputs['book_stock_qty'] == 0){
                            
                            if($findBookStock->transactions->where('transaction_returned_at','=',NULL)->count()){
                                $findBookStock->book_stock_qty = $findBookStock->book_stock_qty - $inputs['book_stock_qty'];
                                $findBookStock->save();
                                \Alert::success('Successfully removed book stock!')->flash();
                            }else {
                                $findBookStock->delete();
                                \Alert::success('Successfully removed book stock!')->flash();
                            }


                            // dd($findBookStock->transactions->where('transaction_returned_at','!=',NULL));
                            // if($findBookStock->transactions->where('transaction_returned_at','!=',NULL)->count()){
                            //     $findBookStock->delete();
                            //     \Alert::success('Successfully removed book stock!')->flash();
                            // }else {
                            //     $findBookStock->book_stock_qty = $findBookStock->book_stock_qty - $inputs['book_stock_qty'];
                            //     $findBookStock->save();
                            //     \Alert::success('Successfully removed book stock!')->flash();
                            // }
                        }elseif($findBookStock->book_stock_qty - $inputs['book_stock_qty'] > 0){
                            $findBookStock->book_stock_qty = $findBookStock->book_stock_qty - $inputs['book_stock_qty'];
                            $findBookStock->save();
                            \Alert::success('Successfully removed book stock!')->flash();
                        }else {
                            \Alert::error('Check your book stock qty, your input more bigger than the actual stock')->flash();
                        }
                        DB::commit();
                    } catch (\Throwable $th) {
                        DB::rollback();
                        \Alert::error($th->getMessage())->flash();
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
