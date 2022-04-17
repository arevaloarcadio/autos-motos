<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">{{ trans('brackets/admin-ui::admin.sidebar.content') }}</li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/vehicle-categories') }}"><i class="nav-icon icon-star"></i> {{ trans('admin.vehicle-category.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/brands') }}"><i class="nav-icon icon-compass"></i> {{ trans('admin.brand.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/categories') }}"><i class="nav-icon icon-graduation"></i> {{ trans('admin.category.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/attributes') }}"><i class="nav-icon icon-magnet"></i> {{ trans('admin.attribute.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/attribute-values') }}"><i class="nav-icon icon-compass"></i> {{ trans('admin.attribute-value.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/stores') }}"><i class="nav-icon icon-globe"></i> {{ trans('admin.store.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/companies') }}"><i class="nav-icon icon-ghost"></i> {{ trans('admin.company.title') }}</a></li>
           <li class="nav-item"><a class="nav-link" href="{{ url('admin/vehicles') }}"><i class="nav-icon icon-ghost"></i> {{ trans('admin.vehicle.title') }}</a></li>
           {{-- Do not delete me :) I'm used for auto-generation menu items --}}

            <li class="nav-title">{{ trans('brackets/admin-ui::admin.sidebar.settings') }}</li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/admin-users') }}"><i class="nav-icon icon-user"></i> {{ __('Manage access') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('admin/translations') }}"><i class="nav-icon icon-location-pin"></i> {{ __('Translations') }}</a></li>
            {{-- Do not delete me :) I'm also used for auto-generation menu items --}}
            {{--<li class="nav-item"><a class="nav-link" href="{{ url('admin/configuration') }}"><i class="nav-icon icon-settings"></i> {{ __('Configuration') }}</a></li>--}}
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
