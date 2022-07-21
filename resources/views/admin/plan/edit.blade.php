@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.plan.actions.edit', ['name' => $plan->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <plan-form
                :action="'{{ $plan->resource_url }}'"
                :data="{{ $plan->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.plan.actions.edit', ['name' => $plan->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.plan.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </plan-form>

        </div>
    
</div>

@endsection