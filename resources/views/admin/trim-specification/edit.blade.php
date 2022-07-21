@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.trim-specification.actions.edit', ['name' => $trimSpecification->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <trim-specification-form
                :action="'{{ $trimSpecification->resource_url }}'"
                :data="{{ $trimSpecification->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.trim-specification.actions.edit', ['name' => $trimSpecification->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.trim-specification.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </trim-specification-form>

        </div>
    
</div>

@endsection