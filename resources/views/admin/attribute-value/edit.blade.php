@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.attribute-value.actions.edit', ['name' => $attributeValue->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <attribute-value-form
                :action="'{{ $attributeValue->resource_url }}'"
                :data="{{ $attributeValue->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.attribute-value.actions.edit', ['name' => $attributeValue->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.attribute-value.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </attribute-value-form>

        </div>
    
</div>

@endsection