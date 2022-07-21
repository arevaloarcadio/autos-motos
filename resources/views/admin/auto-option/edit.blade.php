@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.auto-option.actions.edit', ['name' => $autoOption->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <auto-option-form
                :action="'{{ $autoOption->resource_url }}'"
                :data="{{ $autoOption->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.auto-option.actions.edit', ['name' => $autoOption->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.auto-option.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </auto-option-form>

        </div>
    
</div>

@endsection