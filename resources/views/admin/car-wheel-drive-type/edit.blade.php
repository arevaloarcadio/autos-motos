@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.car-wheel-drive-type.actions.edit', ['name' => $carWheelDriveType->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <car-wheel-drive-type-form
                :action="'{{ $carWheelDriveType->resource_url }}'"
                :data="{{ $carWheelDriveType->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.car-wheel-drive-type.actions.edit', ['name' => $carWheelDriveType->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.car-wheel-drive-type.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </car-wheel-drive-type-form>

        </div>
    
</div>

@endsection