@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.truck-ad.actions.edit', ['name' => $truckAd->first_name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <truck-ad-form
                :action="'{{ $truckAd->resource_url }}'"
                :data="{{ $truckAd->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.truck-ad.actions.edit', ['name' => $truckAd->first_name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.truck-ad.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </truck-ad-form>

        </div>
    
</div>

@endsection