@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.moto-ad-option.actions.edit', ['name' => $motoAdOption->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <moto-ad-option-form
                :action="'{{ $motoAdOption->resource_url }}'"
                :data="{{ $motoAdOption->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.moto-ad-option.actions.edit', ['name' => $motoAdOption->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.moto-ad-option.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </moto-ad-option-form>

        </div>
    
</div>

@endsection