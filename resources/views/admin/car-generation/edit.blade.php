@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.car-generation.actions.edit', ['name' => $carGeneration->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <car-generation-form
                :action="'{{ $carGeneration->resource_url }}'"
                :data="{{ $carGeneration->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.car-generation.actions.edit', ['name' => $carGeneration->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.car-generation.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </car-generation-form>

        </div>
    
</div>

@endsection