@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.ad-image-version.actions.edit', ['name' => $adImageVersion->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <ad-image-version-form
                :action="'{{ $adImageVersion->resource_url }}'"
                :data="{{ $adImageVersion->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.ad-image-version.actions.edit', ['name' => $adImageVersion->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.ad-image-version.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </ad-image-version-form>

        </div>
    
</div>

@endsection