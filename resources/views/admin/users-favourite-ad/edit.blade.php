@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.users-favourite-ad.actions.edit', ['name' => $usersFavouriteAd->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <users-favourite-ad-form
                :action="'{{ $usersFavouriteAd->resource_url }}'"
                :data="{{ $usersFavouriteAd->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.users-favourite-ad.actions.edit', ['name' => $usersFavouriteAd->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.users-favourite-ad.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </users-favourite-ad-form>

        </div>
    
</div>

@endsection