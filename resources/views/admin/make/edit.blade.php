@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.make.actions.edit', ['name' => $make->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <make-form
                :action="'{{ $make->resource_url }}'"
                :data="{{ $make->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.make.actions.edit', ['name' => $make->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.make.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </make-form>

        </div>
    
</div>

@endsection