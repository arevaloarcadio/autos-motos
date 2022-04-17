@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.attribute.actions.edit', ['name' => $attribute->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <attribute-form
                :action="'{{ $attribute->resource_url }}'"
                :data="{{ $attribute->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.attribute.actions.edit', ['name' => $attribute->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.attribute.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </attribute-form>

        </div>
    
</div>

@endsection