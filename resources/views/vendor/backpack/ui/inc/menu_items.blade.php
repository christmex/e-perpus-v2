{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>


<x-backpack::menu-dropdown title="Manage Book" icon="la la-book">
<x-backpack::menu-dropdown-header title="Book" />
    <x-backpack::menu-dropdown-item title="Books" icon="la la-book" :link="backpack_url('book')" />
    <x-backpack::menu-dropdown-item title="Book stocks" icon="la la-box-open" :link="backpack_url('book-stock')" />
    
    <x-backpack::menu-dropdown-header title="Book Settings" />
    <x-backpack::menu-dropdown-item title="Authors" icon="la la-book-reader" :link="backpack_url('author')" />
    <x-backpack::menu-dropdown-item title="Book types" icon="la la-swatchbook" :link="backpack_url('book-type')" />
    <x-backpack::menu-dropdown-item title="Publishers" icon="la la-truck-loading" :link="backpack_url('publisher')" />
    <x-backpack::menu-dropdown-item title="Book locations" icon="la la-boxes" :link="backpack_url('book-location')" />
</x-backpack::menu-dropdown>


<x-backpack::menu-dropdown title="Manage member" icon="la la-users">
    <x-backpack::menu-dropdown-item title="Departments" icon="la la-building" :link="backpack_url('department')" />
    <x-backpack::menu-dropdown-item title="Members" icon="la la-users" :link="backpack_url('member')" />
</x-backpack::menu-dropdown>

<!-- <x-backpack::menu-dropdown title="Settings" icon="la la-cog">
    <x-backpack::menu-dropdown-item title="Penalty statuses" icon="la la-question" :link="backpack_url('penalty-status')" />
</x-backpack::menu-dropdown> -->

<x-backpack::menu-dropdown title="Transaction" icon="la la-exchange-alt">
    <!-- <x-backpack::menu-dropdown-item title="Loan Book" icon="la la-exchange-alt" :link="backpack_url('loan-book')" /> -->
    <x-backpack::menu-dropdown-item title="Transaction" icon="la la-exchange-alt" :link="backpack_url('transaction')" />
    <x-backpack::menu-dropdown-item title="Penalties" icon="la la-coins" :link="backpack_url('penalty')" />
</x-backpack::menu-dropdown>