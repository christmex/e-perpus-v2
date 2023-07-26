{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Authors" icon="la la-question" :link="backpack_url('author')" />
<x-backpack::menu-item title="Book types" icon="la la-question" :link="backpack_url('book-type')" />
<x-backpack::menu-item title="Publishers" icon="la la-question" :link="backpack_url('publisher')" />
<x-backpack::menu-item title="Book locations" icon="la la-question" :link="backpack_url('book-location')" />
<x-backpack::menu-item title="Departments" icon="la la-question" :link="backpack_url('department')" />
<x-backpack::menu-item title="Members" icon="la la-question" :link="backpack_url('member')" />
<x-backpack::menu-item title="Books" icon="la la-question" :link="backpack_url('book')" />