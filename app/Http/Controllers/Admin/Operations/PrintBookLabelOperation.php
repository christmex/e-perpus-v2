<?php

namespace App\Http\Controllers\Admin\Operations;

use Backpack\CRUD\app\Http\Controllers\Operations\Concerns\HasForm;

trait PrintBookLabelOperation
{
    use HasForm;

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupPrintBookLabelRoutes(string $segment, string $routeName, string $controller): void
    {
        $this->formRoutes(
            operationName: 'printBookLabel',
            routesHaveIdSegment: true,
            segment: $segment,
            routeName: $routeName,
            controller: $controller
        );
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupPrintBookLabelDefaults(): void
    {
        $this->formDefaults(
            operationName: 'printBookLabel',
            buttonStack: 'line', // alternatives: top, bottom
            buttonMeta: [
                'icon' => 'la la-print',
                'label' => 'Print Book Label',
                'wrapper' => [
                     'target' => '_blank',
                ],
            ],
        );

        $this->crud->operation('printBookLabel', function () {
            $currentEntry = $this->crud->getCurrentEntry();

            $this->crud->field([
                'name'          => 'book_name',
                'label'         => 'Book Name',
                'type'          => 'text',
                'attributes'    => ['readonly' => 'readonly'],
                'value'         => $currentEntry->book_name,
            ]);
            // dd($currentEntry->bookStock);
            foreach ($currentEntry->bookStock as $key => $value) {
                $this->crud->field([
                    'name'          => 'book_stock_id['.$value->id.']',
                    'label'         => $value->bookLocation->book_location_name,
                    'type'          => 'number',
                    'value'         => $value->book_stock_qty,
                ]);
            }

            $this->crud->removeSaveAction('save_and_back');
            $this->crud->addSaveAction([
                'name' => 'print_book_label',
                'visible' => function ($crud){
                    return $crud->hasAccess('printBookLabel');
                },
                'button_text' => 'Print Book Label',
            ]);
        });
    }

    /**
     * Method to handle the GET request and display the View with a Backpack form
     *
     */
    public function getPrintBookLabelForm(?int $id = null) : \Illuminate\Contracts\View\View
    {
        $this->crud->hasAccessOrFail('printBookLabel');

        return $this->formView($id);
    }

    /**
    * Method to handle the POST request and perform the operation
    *
    * @return array|\Illuminate\Http\RedirectResponse
    */
    public function postPrintBookLabelForm(?int $id = null)
    {
        $this->crud->hasAccessOrFail('printBookLabel');

        if ($id) {
            // Get entry ID from Request (makes sure its the last ID for nested resources)
            $id = $this->crud->getCurrentEntryId() ?? $id;
            $entry = $this->crud->getEntryWithLocale($id);
        }

        $request = request()->all();

        // dd($request['book_stock_id'][3]);
        // $entry->bookStock = $entry->bookStock->where('id',3)->take($request['book_stock_id'][3]);
        // dd($entry);


        // for ($i=0; $i < count($request['book_stock_id']); $i++) { 
        //     $entry->bookStock = $entry->bookStock->where('id')->limit($request['book_stock_id'][$i]);
        // }
        // dd();
        return view('print_book_label',compact('entry','request'));

    }
}
