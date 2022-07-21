@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.car-generation.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <car-generation-form
            :action="'{{ url('admin/car-generations') }}'"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.car-generation.actions.create') }}
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