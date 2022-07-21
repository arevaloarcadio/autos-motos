@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.car-transmission-type.actions.edit', ['name' => $carTransmissionType->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <car-transmission-type-form
                :action="'{{ $carTransmissionType->resource_url }}'"
                :data="{{ $carTransmissionType->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.car-transmission-type.actions.edit', ['name' => $carTransmissionType->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.car-transmission-type.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </car-transmission-type-form>

        </div>
    
</div>

@endsection