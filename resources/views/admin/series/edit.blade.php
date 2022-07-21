@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.series.actions.edit', ['name' => $series->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <series-form
                :action="'{{ $series->resource_url }}'"
                :data="{{ $series->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.series.actions.edit', ['name' => $series->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.series.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </series-form>

        </div>
    
</div>

@endsection