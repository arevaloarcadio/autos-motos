@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.auto-ad-option.actions.edit', ['name' => $autoAdOption->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <auto-ad-option-form
                :action="'{{ $autoAdOption->resource_url }}'"
                :data="{{ $autoAdOption->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.auto-ad-option.actions.edit', ['name' => $autoAdOption->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.auto-ad-option.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </auto-ad-option-form>

        </div>
    
</div>

@endsection