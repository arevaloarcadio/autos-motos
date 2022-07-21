@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.users-favourite-ad-search.actions.edit', ['name' => $usersFavouriteAdSearch->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <users-favourite-ad-search-form
                :action="'{{ $usersFavouriteAdSearch->resource_url }}'"
                :data="{{ $usersFavouriteAdSearch->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.users-favourite-ad-search.actions.edit', ['name' => $usersFavouriteAdSearch->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.users-favourite-ad-search.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </users-favourite-ad-search-form>

        </div>
    
</div>

@endsection