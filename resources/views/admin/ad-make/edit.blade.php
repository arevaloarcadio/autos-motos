@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.ad-make.actions.edit', ['name' => $adMake->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <ad-make-form
                :action="'{{ $adMake->resource_url }}'"
                :data="{{ $adMake->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.ad-make.actions.edit', ['name' => $adMake->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.ad-make.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </ad-make-form>

        </div>
    
</div>

@endsection