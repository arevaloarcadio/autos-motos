@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.option.actions.edit', ['name' => $option->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <option-form
                :action="'{{ $option->resource_url }}'"
                :data="{{ $option->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.option.actions.edit', ['name' => $option->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.option.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </option-form>

        </div>
    
</div>

@endsection