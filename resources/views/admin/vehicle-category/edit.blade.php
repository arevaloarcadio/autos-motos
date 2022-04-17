@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.vehicle-category.actions.edit', ['name' => $vehicleCategory->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <vehicle-category-form
                :action="'{{ $vehicleCategory->resource_url }}'"
                :data="{{ $vehicleCategory->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.vehicle-category.actions.edit', ['name' => $vehicleCategory->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.vehicle-category.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </vehicle-category-form>

        </div>
    
</div>

@endsection