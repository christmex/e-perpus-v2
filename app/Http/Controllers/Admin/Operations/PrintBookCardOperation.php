<?php

namespace App\Http\Controllers\Admin\Operations;

use Backpack\CRUD\app\Http\Controllers\Operations\Concerns\HasForm;

trait PrintBookCardOperation
{
    use HasForm;

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupPrintBookCardRoutes(string $segment, string $routeName, string $controller): void
    {
        $this->formRoutes(
            operationName: 'printBookCard',
            routesHaveIdSegment: true,
            segment: $segment,
            routeName: $routeName,
            controller: $controller
        );
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupPrintBookCardDefaults(): void
    {
        $this->formDefaults(
            operationName: 'printBookCard',
            buttonStack: 'line', // alternatives: top, bottom
            buttonMeta: [
                'icon' => 'la la-print',
                'label' => 'Print Book Card',
                'wrapper' => [
                     'target' => '_blank',
                ],
            ],
        );

        $this->crud->operation('printBookCard', function () {
            $currentEntry = $this->crud->getCurrentEntry();

            $this->crud->field([
                'name'          => 'book_name',
                'label'         => 'Book Name',
                'type'          => 'text',
                'attributes'    => ['readonly' => 'readonly'],
                'value'         => $currentEntry->book_name,
            ]);
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
                'name' => 'print_book_card',
                'visible' => function ($crud){
                    return $crud->hasAccess('printBookCard');
                },
                'button_text' => 'Print Book Card',
            ]);
        });
    }

    /**
     * Method to handle the GET request and display the View with a Backpack form
     *
     */
    public function getPrintBookCardForm(?int $id = null) : \Illuminate\Contracts\View\View
    {
        $this->crud->hasAccessOrFail('printBookCard');

        return $this->formView($id);
    }

    /**
    * Method to handle the POST request and perform the operation
    *
    * @return array|\Illuminate\Http\RedirectResponse
    */
    public function postPrintBookCardForm(?int $id = null)
    {
        $this->crud->hasAccessOrFail('printBookCard');

        if ($id) {
            // Get entry ID from Request (makes sure its the last ID for nested resources)
            $id = $this->crud->getCurrentEntryId() ?? $id;
            $entry = $this->crud->getEntryWithLocale($id);
        }

        $request = request()->all();

        return view('print_book_card',compact('entry','request'));

    }
}
