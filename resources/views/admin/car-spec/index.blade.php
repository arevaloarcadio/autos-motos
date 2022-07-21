@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.car-spec.actions.index'))

@section('body')

    <car-spec-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/car-specs') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.car-spec.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/car-specs/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.car-spec.actions.create') }}</a>
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}" v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" @click="filter('search', search)"><i class="fa fa-search"></i>&nbsp; {{ trans('brackets/admin-ui::admin.btn.search') }}</button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-auto form-group ">
                                        <select class="form-control" v-model="pagination.state.per_page">
                                            
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-hover table-listing">
                                <thead>
                                    <tr>
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th is='sortable' :column="'id'">{{ trans('admin.car-spec.columns.id') }}</th>
                                        <th is='sortable' :column="'car_make_id'">{{ trans('admin.car-spec.columns.car_make_id') }}</th>
                                        <th is='sortable' :column="'car_model_id'">{{ trans('admin.car-spec.columns.car_model_id') }}</th>
                                        <th is='sortable' :column="'car_generation_id'">{{ trans('admin.car-spec.columns.car_generation_id') }}</th>
                                        <th is='sortable' :column="'car_body_type_id'">{{ trans('admin.car-spec.columns.car_body_type_id') }}</th>
                                        <th is='sortable' :column="'engine'">{{ trans('admin.car-spec.columns.engine') }}</th>
                                        <th is='sortable' :column="'doors'">{{ trans('admin.car-spec.columns.doors') }}</th>
                                        <th is='sortable' :column="'doors_min'">{{ trans('admin.car-spec.columns.doors_min') }}</th>
                                        <th is='sortable' :column="'doors_max'">{{ trans('admin.car-spec.columns.doors_max') }}</th>
                                        <th is='sortable' :column="'power_hp'">{{ trans('admin.car-spec.columns.power_hp') }}</th>
                                        <th is='sortable' :column="'power_rpm'">{{ trans('admin.car-spec.columns.power_rpm') }}</th>
                                        <th is='sortable' :column="'power_rpm_min'">{{ trans('admin.car-spec.columns.power_rpm_min') }}</th>
                                        <th is='sortable' :column="'power_rpm_max'">{{ trans('admin.car-spec.columns.power_rpm_max') }}</th>
                                        <th is='sortable' :column="'engine_displacement'">{{ trans('admin.car-spec.columns.engine_displacement') }}</th>
                                        <th is='sortable' :column="'production_start_year'">{{ trans('admin.car-spec.columns.production_start_year') }}</th>
                                        <th is='sortable' :column="'production_end_year'">{{ trans('admin.car-spec.columns.production_end_year') }}</th>
                                        <th is='sortable' :column="'car_fuel_type_id'">{{ trans('admin.car-spec.columns.car_fuel_type_id') }}</th>
                                        <th is='sortable' :column="'car_transmission_type_id'">{{ trans('admin.car-spec.columns.car_transmission_type_id') }}</th>
                                        <th is='sortable' :column="'gears'">{{ trans('admin.car-spec.columns.gears') }}</th>
                                        <th is='sortable' :column="'car_wheel_drive_type_id'">{{ trans('admin.car-spec.columns.car_wheel_drive_type_id') }}</th>
                                        <th is='sortable' :column="'battery_capacity'">{{ trans('admin.car-spec.columns.battery_capacity') }}</th>
                                        <th is='sortable' :column="'electric_power_hp'">{{ trans('admin.car-spec.columns.electric_power_hp') }}</th>
                                        <th is='sortable' :column="'electric_power_rpm'">{{ trans('admin.car-spec.columns.electric_power_rpm') }}</th>
                                        <th is='sortable' :column="'electric_power_rpm_min'">{{ trans('admin.car-spec.columns.electric_power_rpm_min') }}</th>
                                        <th is='sortable' :column="'electric_power_rpm_max'">{{ trans('admin.car-spec.columns.electric_power_rpm_max') }}</th>
                                        <th is='sortable' :column="'external_id'">{{ trans('admin.car-spec.columns.external_id') }}</th>
                                        <th is='sortable' :column="'last_external_update'">{{ trans('admin.car-spec.columns.last_external_update') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="29">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/car-specs')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/car-specs/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td>

                                    <td>@{{ item.id }}</td>
                                        <td>@{{ item.car_make_id }}</td>
                                        <td>@{{ item.car_model_id }}</td>
                                        <td>@{{ item.car_generation_id }}</td>
                                        <td>@{{ item.car_body_type_id }}</td>
                                        <td>@{{ item.engine }}</td>
                                        <td>@{{ item.doors }}</td>
                                        <td>@{{ item.doors_min }}</td>
                                        <td>@{{ item.doors_max }}</td>
                                        <td>@{{ item.power_hp }}</td>
                                        <td>@{{ item.power_rpm }}</td>
                                        <td>@{{ item.power_rpm_min }}</td>
                                        <td>@{{ item.power_rpm_max }}</td>
                                        <td>@{{ item.engine_displacement }}</td>
                                        <td>@{{ item.production_start_year | date }}</td>
                                        <td>@{{ item.production_end_year | date }}</td>
                                        <td>@{{ item.car_fuel_type_id }}</td>
                                        <td>@{{ item.car_transmission_type_id }}</td>
                                        <td>@{{ item.gears }}</td>
                                        <td>@{{ item.car_wheel_drive_type_id }}</td>
                                        <td>@{{ item.battery_capacity }}</td>
                                        <td>@{{ item.electric_power_hp }}</td>
                                        <td>@{{ item.electric_power_rpm }}</td>
                                        <td>@{{ item.electric_power_rpm_min }}</td>
                                        <td>@{{ item.electric_power_rpm_max }}</td>
                                        <td>@{{ item.external_id }}</td>
                                        <td>@{{ item.last_external_update | datetime }}</td>
                                        
                                        <td>
                                            <div class="row no-gutters">
                                                <div class="col-auto">
                                                    <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
                                                </div>
                                                <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i class="fa fa-trash-o"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/car-specs/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.car-spec.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </car-spec-listing>

@endsection