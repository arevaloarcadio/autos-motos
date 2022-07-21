@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.moto-ad.actions.edit', ['name' => $motoAd->first_name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <moto-ad-form
                :action="'{{ $motoAd->resource_url }}'"
                :data="{{ $motoAd->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.moto-ad.actions.edit', ['name' => $motoAd->first_name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.moto-ad.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </moto-ad-form>

        </div>
    
</div>

@endsection