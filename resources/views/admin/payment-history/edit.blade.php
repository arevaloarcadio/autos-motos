@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.payment-history.actions.edit', ['name' => $paymentHistory->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <payment-history-form
                :action="'{{ $paymentHistory->resource_url }}'"
                :data="{{ $paymentHistory->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.payment-history.actions.edit', ['name' => $paymentHistory->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.payment-history.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </payment-history-form>

        </div>
    
</div>

@endsection