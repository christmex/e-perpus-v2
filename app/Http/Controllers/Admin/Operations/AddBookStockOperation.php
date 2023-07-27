<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\BookStock;
use App\Http\Requests\BookStockRequest;
use Backpack\CRUD\app\Http\Controllers\Operations\Concerns\HasForm;
// use app\Http\Requests\BookStockRequest as StoreRequest;

trait AddBookStockOperation
{
    use HasForm;

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupAddBookStockRoutes(string $segment, string $routeName, string $controller): void
    {
        $this->formRoutes(
            operationName: 'addBookStock',
            routesHaveIdSegment: true,
            segment: $segment,
            routeName: $routeName,
            controller: $controller
        );
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupAddBookStockDefaults(): void
    {
        $this->formDefaults(
            operationName: 'addBookStock',
            buttonStack: 'line', // alternatives: top, bottom
            buttonMeta: [
                'icon' => 'la la-book',
                'label' => 'Add Book Stock',
            ],
        );

        $this->crud->operation('addBookStock', function () {
            $this->crud->setValidation(BookStockRequest::class);

            $currentEntry = $this->crud->getCurrentEntry();

            $this->crud->field([
                'name'  => 'book_id',
                'type'  => 'hidden',
                'tab'   => 'Form Add Stock',
                'value' => $currentEntry->id,
            ]);
            $this->crud->field([
                'name'          => 'book_name',
                'label'         => 'Book Name',
                'type'          => 'text',
                'tab'           => 'Form Add Stock',
                'attributes'    => ['readonly' => 'readonly'],
                'value'         => $currentEntry->book_name,
            ]);

            $this->crud->field([
                'name'          => 'book_location_id', // the relationship name in your Migration
                'type'          => 'select',
                'model'         => 'App\Models\BookLocation', // the relationship name in your Model
                'allows_null'   => false,
                'tab'           => 'Form Add Stock',
                'attribute'     => 'book_location_name',
            ]);
            $this->crud->field([
                'name'          => 'book_stock_qty',
                'type'          => 'number',
                'tab'           => 'Form Add Stock',
                'attributes'    => ['min' => 1],
            ]);
            $this->crud->field([
                'name'          => 'book_description',
                'type'          => 'textarea',
                'tab'           => 'Form Add Stock',
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
    public function getAddBookStockForm(?int $id = null) : \Illuminate\Contracts\View\View
    {
        $this->crud->hasAccessOrFail('addBookStock');

        return $this->formView($id);
    }

    /**
    * Method to handle the POST request and perform the operation
    *
    * @return array|\Illuminate\Http\RedirectResponse
    */
    public function postAddBookStockForm(?int $id = null)
    {
        $this->crud->hasAccessOrFail('addBookStock');

        return $this->formAction($id, function ($inputs, $entry) {
            // You logic goes here...
            // dd('got to ' . __METHOD__, $inputs, $entry);
            
            // check jika di book_location udh ada datanya, berarti stocknya ditambah, jika tidak ada datanya berarti buat baru, bgtu juga untuk delete
            $findBookStock = BookStock::where('book_id',$inputs['book_id'])->where('book_location_id', $inputs['book_location_id'])->first();
            if(!empty($findBookStock)){
                $findBookStock->book_stock_qty = $findBookStock->book_stock_qty + $inputs['book_stock_qty'];
                $findBookStock->save();
            }else {
                BookStock::create($inputs);
            }

            // insert ke book history, penambahannya

            // show a success message
            \Alert::success('Successfully added book stock')->flash();
        });
    }
}
