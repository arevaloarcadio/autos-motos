@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.truck-ad.actions.index'))

@section('body')

    <truck-ad-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/truck-ads') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.truck-ad.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/truck-ads/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.truck-ad.actions.create') }}</a>
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

                                        <th is='sortable' :column="'id'">{{ trans('admin.truck-ad.columns.id') }}</th>
                                        <th is='sortable' :column="'ad_id'">{{ trans('admin.truck-ad.columns.ad_id') }}</th>
                                        <th is='sortable' :column="'make_id'">{{ trans('admin.truck-ad.columns.make_id') }}</th>
                                        <th is='sortable' :column="'custom_make'">{{ trans('admin.truck-ad.columns.custom_make') }}</th>
                                        <th is='sortable' :column="'model'">{{ trans('admin.truck-ad.columns.model') }}</th>
                                        <th is='sortable' :column="'truck_type'">{{ trans('admin.truck-ad.columns.truck_type') }}</th>
                                        <th is='sortable' :column="'fuel_type_id'">{{ trans('admin.truck-ad.columns.fuel_type_id') }}</th>
                                        <th is='sortable' :column="'vehicle_category_id'">{{ trans('admin.truck-ad.columns.vehicle_category_id') }}</th>
                                        <th is='sortable' :column="'transmission_type_id'">{{ trans('admin.truck-ad.columns.transmission_type_id') }}</th>
                                        <th is='sortable' :column="'cab'">{{ trans('admin.truck-ad.columns.cab') }}</th>
                                        <th is='sortable' :column="'construction_year'">{{ trans('admin.truck-ad.columns.construction_year') }}</th>
                                        <th is='sortable' :column="'first_registration_month'">{{ trans('admin.truck-ad.columns.first_registration_month') }}</th>
                                        <th is='sortable' :column="'first_registration_year'">{{ trans('admin.truck-ad.columns.first_registration_year') }}</th>
                                        <th is='sortable' :column="'inspection_valid_until_month'">{{ trans('admin.truck-ad.columns.inspection_valid_until_month') }}</th>
                                        <th is='sortable' :column="'inspection_valid_until_year'">{{ trans('admin.truck-ad.columns.inspection_valid_until_year') }}</th>
                                        <th is='sortable' :column="'owners'">{{ trans('admin.truck-ad.columns.owners') }}</th>
                                        <th is='sortable' :column="'construction_height_mm'">{{ trans('admin.truck-ad.columns.construction_height_mm') }}</th>
                                        <th is='sortable' :column="'lifting_height_mm'">{{ trans('admin.truck-ad.columns.lifting_height_mm') }}</th>
                                        <th is='sortable' :column="'lifting_capacity_kg_m'">{{ trans('admin.truck-ad.columns.lifting_capacity_kg_m') }}</th>
                                        <th is='sortable' :column="'permanent_total_weight_kg'">{{ trans('admin.truck-ad.columns.permanent_total_weight_kg') }}</th>
                                        <th is='sortable' :column="'allowed_pulling_weight_kg'">{{ trans('admin.truck-ad.columns.allowed_pulling_weight_kg') }}</th>
                                        <th is='sortable' :column="'payload_kg'">{{ trans('admin.truck-ad.columns.payload_kg') }}</th>
                                        <th is='sortable' :column="'max_weight_allowed_kg'">{{ trans('admin.truck-ad.columns.max_weight_allowed_kg') }}</th>
                                        <th is='sortable' :column="'empty_weight_kg'">{{ trans('admin.truck-ad.columns.empty_weight_kg') }}</th>
                                        <th is='sortable' :column="'loading_space_length_mm'">{{ trans('admin.truck-ad.columns.loading_space_length_mm') }}</th>
                                        <th is='sortable' :column="'loading_space_width_mm'">{{ trans('admin.truck-ad.columns.loading_space_width_mm') }}</th>
                                        <th is='sortable' :column="'loading_space_height_mm'">{{ trans('admin.truck-ad.columns.loading_space_height_mm') }}</th>
                                        <th is='sortable' :column="'loading_volume_m3'">{{ trans('admin.truck-ad.columns.loading_volume_m3') }}</th>
                                        <th is='sortable' :column="'load_capacity_kg'">{{ trans('admin.truck-ad.columns.load_capacity_kg') }}</th>
                                        <th is='sortable' :column="'operating_weight_kg'">{{ trans('admin.truck-ad.columns.operating_weight_kg') }}</th>
                                        <th is='sortable' :column="'operating_hours'">{{ trans('admin.truck-ad.columns.operating_hours') }}</th>
                                        <th is='sortable' :column="'axes'">{{ trans('admin.truck-ad.columns.axes') }}</th>
                                        <th is='sortable' :column="'wheel_formula'">{{ trans('admin.truck-ad.columns.wheel_formula') }}</th>
                                        <th is='sortable' :column="'hydraulic_system'">{{ trans('admin.truck-ad.columns.hydraulic_system') }}</th>
                                        <th is='sortable' :column="'seats'">{{ trans('admin.truck-ad.columns.seats') }}</th>
                                        <th is='sortable' :column="'mileage'">{{ trans('admin.truck-ad.columns.mileage') }}</th>
                                        <th is='sortable' :column="'power_kw'">{{ trans('admin.truck-ad.columns.power_kw') }}</th>
                                        <th is='sortable' :column="'emission_class'">{{ trans('admin.truck-ad.columns.emission_class') }}</th>
                                        <th is='sortable' :column="'fuel_consumption'">{{ trans('admin.truck-ad.columns.fuel_consumption') }}</th>
                                        <th is='sortable' :column="'co2_emissions'">{{ trans('admin.truck-ad.columns.co2_emissions') }}</th>
                                        <th is='sortable' :column="'condition'">{{ trans('admin.truck-ad.columns.condition') }}</th>
                                        <th is='sortable' :column="'interior_color'">{{ trans('admin.truck-ad.columns.interior_color') }}</th>
                                        <th is='sortable' :column="'exterior_color'">{{ trans('admin.truck-ad.columns.exterior_color') }}</th>
                                        <th is='sortable' :column="'price'">{{ trans('admin.truck-ad.columns.price') }}</th>
                                        <th is='sortable' :column="'price_contains_vat'">{{ trans('admin.truck-ad.columns.price_contains_vat') }}</th>
                                        <th is='sortable' :column="'dealer_id'">{{ trans('admin.truck-ad.columns.dealer_id') }}</th>
                                        <th is='sortable' :column="'dealer_show_room_id'">{{ trans('admin.truck-ad.columns.dealer_show_room_id') }}</th>
                                        <th is='sortable' :column="'first_name'">{{ trans('admin.truck-ad.columns.first_name') }}</th>
                                        <th is='sortable' :column="'last_name'">{{ trans('admin.truck-ad.columns.last_name') }}</th>
                                        <th is='sortable' :column="'email_address'">{{ trans('admin.truck-ad.columns.email_address') }}</th>
                                        <th is='sortable' :column="'zip_code'">{{ trans('admin.truck-ad.columns.zip_code') }}</th>
                                        <th is='sortable' :column="'city'">{{ trans('admin.truck-ad.columns.city') }}</th>
                                        <th is='sortable' :column="'country'">{{ trans('admin.truck-ad.columns.country') }}</th>
                                        <th is='sortable' :column="'mobile_number'">{{ trans('admin.truck-ad.columns.mobile_number') }}</th>
                                        <th is='sortable' :column="'landline_number'">{{ trans('admin.truck-ad.columns.landline_number') }}</th>
                                        <th is='sortable' :column="'whatsapp_number'">{{ trans('admin.truck-ad.columns.whatsapp_number') }}</th>
                                        <th is='sortable' :column="'youtube_link'">{{ trans('admin.truck-ad.columns.youtube_link') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="59">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/truck-ads')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/truck-ads/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td>@{{ item.ad_id }}</td>
                                        <td>@{{ item.make_id }}</td>
                                        <td>@{{ item.custom_make }}</td>
                                        <td>@{{ item.model }}</td>
                                        <td>@{{ item.truck_type }}</td>
                                        <td>@{{ item.fuel_type_id }}</td>
                                        <td>@{{ item.vehicle_category_id }}</td>
                                        <td>@{{ item.transmission_type_id }}</td>
                                        <td>@{{ item.cab }}</td>
                                        <td>@{{ item.construction_year }}</td>
                                        <td>@{{ item.first_registration_month }}</td>
                                        <td>@{{ item.first_registration_year }}</td>
                                        <td>@{{ item.inspection_valid_until_month }}</td>
                                        <td>@{{ item.inspection_valid_until_year }}</td>
                                        <td>@{{ item.owners }}</td>
                                        <td>@{{ item.construction_height_mm }}</td>
                                        <td>@{{ item.lifting_height_mm }}</td>
                                        <td>@{{ item.lifting_capacity_kg_m }}</td>
                                        <td>@{{ item.permanent_total_weight_kg }}</td>
                                        <td>@{{ item.allowed_pulling_weight_kg }}</td>
                                        <td>@{{ item.payload_kg }}</td>
                                        <td>@{{ item.max_weight_allowed_kg }}</td>
                                        <td>@{{ item.empty_weight_kg }}</td>
                                        <td>@{{ item.loading_space_length_mm }}</td>
                                        <td>@{{ item.loading_space_width_mm }}</td>
                                        <td>@{{ item.loading_space_height_mm }}</td>
                                        <td>@{{ item.loading_volume_m3 }}</td>
                                        <td>@{{ item.load_capacity_kg }}</td>
                                        <td>@{{ item.operating_weight_kg }}</td>
                                        <td>@{{ item.operating_hours }}</td>
                                        <td>@{{ item.axes }}</td>
                                        <td>@{{ item.wheel_formula }}</td>
                                        <td>@{{ item.hydraulic_system }}</td>
                                        <td>@{{ item.seats }}</td>
                                        <td>@{{ item.mileage }}</td>
                                        <td>@{{ item.power_kw }}</td>
                                        <td>@{{ item.emission_class }}</td>
                                        <td>@{{ item.fuel_consumption }}</td>
                                        <td>@{{ item.co2_emissions }}</td>
                                        <td>@{{ item.condition }}</td>
                                        <td>@{{ item.interior_color }}</td>
                                        <td>@{{ item.exterior_color }}</td>
                                        <td>@{{ item.price }}</td>
                                        <td>@{{ item.price_contains_vat }}</td>
                                        <td>@{{ item.dealer_id }}</td>
                                        <td>@{{ item.dealer_show_room_id }}</td>
                                        <td>@{{ item.first_name }}</td>
                                        <td>@{{ item.last_name }}</td>
                                        <td>@{{ item.email_address }}</td>
                                        <td>@{{ item.zip_code }}</td>
                                        <td>@{{ item.city }}</td>
                                        <td>@{{ item.country }}</td>
                                        <td>@{{ item.mobile_number }}</td>
                                        <td>@{{ item.landline_number }}</td>
                                        <td>@{{ item.whatsapp_number }}</td>
                                        <td>@{{ item.youtube_link }}</td>
                                        
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
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/truck-ads/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.truck-ad.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </truck-ad-listing>

@endsection