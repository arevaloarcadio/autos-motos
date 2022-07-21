@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.dealer.actions.edit', ['name' => $dealer->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <dealer-form
                :action="'{{ $dealer->resource_url }}'"
                :data="{{ $dealer->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.dealer.actions.edit', ['name' => $dealer->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.dealer.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </dealer-form>

        </div>
    
</div>

@endsection