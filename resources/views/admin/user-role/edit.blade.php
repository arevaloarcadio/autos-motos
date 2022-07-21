@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-role.actions.edit', ['name' => $userRole->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <user-role-form
                :action="'{{ $userRole->resource_url }}'"
                :data="{{ $userRole->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.user-role.actions.edit', ['name' => $userRole->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.user-role.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </user-role-form>

        </div>
    
</div>

@endsection