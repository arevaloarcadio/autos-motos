@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.characteristic-promotion-plan.actions.edit', ['name' => $characteristicPromotionPlan->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <characteristic-promotion-plan-form
                :action="'{{ $characteristicPromotionPlan->resource_url }}'"
                :data="{{ $characteristicPromotionPlan->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.characteristic-promotion-plan.actions.edit', ['name' => $characteristicPromotionPlan->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.characteristic-promotion-plan.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </characteristic-promotion-plan-form>

        </div>
    
</div>

@endsection