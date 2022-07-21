@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.market.actions.edit', ['name' => $market->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <market-form
                :action="'{{ $market->resource_url }}'"
                :data="{{ $market->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.market.actions.edit', ['name' => $market->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.market.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </market-form>

        </div>
    
</div>

@endsection