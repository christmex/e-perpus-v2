<div>
    {{-- Do your work, then step back. --}}
    <div class="form-group position-relative">
        <input type="hidden" name="{{ $field['name'] }}" wire:model="field_value">
        <input type="text"
            wire:model.debounce.500ms="form_search"
            placeholder="Search {{$field['label']}}"
            @include('crud::fields.inc.attributes') 
        >
        @if(!empty($form_search) && !empty($search_result))
            <ul class="list-group position-absolute bg-white" style="z-index:999; width:100%; box-shadow:0 1px 2px 0 rgba(0, 0, 0, 0.05)">
                @if(!empty($search_result))
                    @foreach($search_result as $item)
                        <li class="list-group-item"style="cursor:pointer" wire:click="setValue({{$item->id}},'{{ $item->{$field["attribute"]} }}')">{{ $item->{$field['attribute']} }}</li>
                    @endforeach
                @endif
                <li class="list-group-item disabled">{{$field['hint']}}</li>
            </ul>
        @endif
    </div>
</div>