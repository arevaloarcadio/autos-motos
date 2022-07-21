@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.sub-characteristic.actions.edit', ['name' => $subCharacteristic->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <sub-characteristic-form
                :action="'{{ $subCharacteristic->resource_url }}'"
                :data="{{ $subCharacteristic->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.sub-characteristic.actions.edit', ['name' => $subCharacteristic->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.sub-characteristic.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </sub-characteristic-form>

        </div>
    
</div>

@endsection