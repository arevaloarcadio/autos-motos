@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.characteristic-plan.actions.edit', ['name' => $characteristicPlan->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <characteristic-plan-form
                :action="'{{ $characteristicPlan->resource_url }}'"
                :data="{{ $characteristicPlan->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.characteristic-plan.actions.edit', ['name' => $characteristicPlan->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.characteristic-plan.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </characteristic-plan-form>

        </div>
    
</div>

@endsection