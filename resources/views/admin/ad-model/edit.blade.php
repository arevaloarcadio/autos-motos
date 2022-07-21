@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.ad-model.actions.edit', ['name' => $adModel->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <ad-model-form
                :action="'{{ $adModel->resource_url }}'"
                :data="{{ $adModel->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.ad-model.actions.edit', ['name' => $adModel->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.ad-model.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </ad-model-form>

        </div>
    
</div>

@endsection