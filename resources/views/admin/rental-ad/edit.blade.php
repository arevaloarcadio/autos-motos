@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.rental-ad.actions.edit', ['name' => $rentalAd->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <rental-ad-form
                :action="'{{ $rentalAd->resource_url }}'"
                :data="{{ $rentalAd->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.rental-ad.actions.edit', ['name' => $rentalAd->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.rental-ad.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </rental-ad-form>

        </div>
    
</div>

@endsection