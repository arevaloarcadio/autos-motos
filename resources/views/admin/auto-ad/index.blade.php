@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.auto-ad.actions.index'))

@section('body')

    <auto-ad-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/auto-ads') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.auto-ad.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/auto-ads/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.auto-ad.actions.create') }}</a>
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

                                        <th is='sortable' :column="'id'">{{ trans('admin.auto-ad.columns.id') }}</th>
                                        <th is='sortable' :column="'ad_id'">{{ trans('admin.auto-ad.columns.ad_id') }}</th>
                                        <th is='sortable' :column="'price'">{{ trans('admin.auto-ad.columns.price') }}</th>
                                        <th is='sortable' :column="'price_contains_vat'">{{ trans('admin.auto-ad.columns.price_contains_vat') }}</th>
                                        <th is='sortable' :column="'vin'">{{ trans('admin.auto-ad.columns.vin') }}</th>
                                        <th is='sortable' :column="'doors'">{{ trans('admin.auto-ad.columns.doors') }}</th>
                                        <th is='sortable' :column="'mileage'">{{ trans('admin.auto-ad.columns.mileage') }}</th>
                                        <th is='sortable' :column="'exterior_color'">{{ trans('admin.auto-ad.columns.exterior_color') }}</th>
                                        <th is='sortable' :column="'interior_color'">{{ trans('admin.auto-ad.columns.interior_color') }}</th>
                                        <th is='sortable' :column="'condition'">{{ trans('admin.auto-ad.columns.condition') }}</th>
                                        <th is='sortable' :column="'dealer_id'">{{ trans('admin.auto-ad.columns.dealer_id') }}</th>
                                        <th is='sortable' :column="'dealer_show_room_id'">{{ trans('admin.auto-ad.columns.dealer_show_room_id') }}</th>
                                        <th is='sortable' :column="'first_name'">{{ trans('admin.auto-ad.columns.first_name') }}</th>
                                        <th is='sortable' :column="'last_name'">{{ trans('admin.auto-ad.columns.last_name') }}</th>
                                        <th is='sortable' :column="'email_address'">{{ trans('admin.auto-ad.columns.email_address') }}</th>
                                        <th is='sortable' :column="'zip_code'">{{ trans('admin.auto-ad.columns.zip_code') }}</th>
                                        <th is='sortable' :column="'city'">{{ trans('admin.auto-ad.columns.city') }}</th>
                                        <th is='sortable' :column="'country'">{{ trans('admin.auto-ad.columns.country') }}</th>
                                        <th is='sortable' :column="'mobile_number'">{{ trans('admin.auto-ad.columns.mobile_number') }}</th>
                                        <th is='sortable' :column="'landline_number'">{{ trans('admin.auto-ad.columns.landline_number') }}</th>
                                        <th is='sortable' :column="'whatsapp_number'">{{ trans('admin.auto-ad.columns.whatsapp_number') }}</th>
                                        <th is='sortable' :column="'youtube_link'">{{ trans('admin.auto-ad.columns.youtube_link') }}</th>
                                        <th is='sortable' :column="'ad_fuel_type_id'">{{ trans('admin.auto-ad.columns.ad_fuel_type_id') }}</th>
                                        <th is='sortable' :column="'ad_body_type_id'">{{ trans('admin.auto-ad.columns.ad_body_type_id') }}</th>
                                        <th is='sortable' :column="'ad_transmission_type_id'">{{ trans('admin.auto-ad.columns.ad_transmission_type_id') }}</th>
                                        <th is='sortable' :column="'ad_drive_type_id'">{{ trans('admin.auto-ad.columns.ad_drive_type_id') }}</th>
                                        <th is='sortable' :column="'first_registration_month'">{{ trans('admin.auto-ad.columns.first_registration_month') }}</th>
                                        <th is='sortable' :column="'first_registration_year'">{{ trans('admin.auto-ad.columns.first_registration_year') }}</th>
                                        <th is='sortable' :column="'engine_displacement'">{{ trans('admin.auto-ad.columns.engine_displacement') }}</th>
                                        <th is='sortable' :column="'power_hp'">{{ trans('admin.auto-ad.columns.power_hp') }}</th>
                                        <th is='sortable' :column="'owners'">{{ trans('admin.auto-ad.columns.owners') }}</th>
                                        <th is='sortable' :column="'inspection_valid_until_month'">{{ trans('admin.auto-ad.columns.inspection_valid_until_month') }}</th>
                                        <th is='sortable' :column="'inspection_valid_until_year'">{{ trans('admin.auto-ad.columns.inspection_valid_until_year') }}</th>
                                        <th is='sortable' :column="'make_id'">{{ trans('admin.auto-ad.columns.make_id') }}</th>
                                        <th is='sortable' :column="'model_id'">{{ trans('admin.auto-ad.columns.model_id') }}</th>
                                        <th is='sortable' :column="'generation_id'">{{ trans('admin.auto-ad.columns.generation_id') }}</th>
                                        <th is='sortable' :column="'series_id'">{{ trans('admin.auto-ad.columns.series_id') }}</th>
                                        <th is='sortable' :column="'trim_id'">{{ trans('admin.auto-ad.columns.trim_id') }}</th>
                                        <th is='sortable' :column="'equipment_id'">{{ trans('admin.auto-ad.columns.equipment_id') }}</th>
                                        <th is='sortable' :column="'additional_vehicle_info'">{{ trans('admin.auto-ad.columns.additional_vehicle_info') }}</th>
                                        <th is='sortable' :column="'seats'">{{ trans('admin.auto-ad.columns.seats') }}</th>
                                        <th is='sortable' :column="'fuel_consumption'">{{ trans('admin.auto-ad.columns.fuel_consumption') }}</th>
                                        <th is='sortable' :column="'co2_emissions'">{{ trans('admin.auto-ad.columns.co2_emissions') }}</th>
                                        <th is='sortable' :column="'latitude'">{{ trans('admin.auto-ad.columns.latitude') }}</th>
                                        <th is='sortable' :column="'longitude'">{{ trans('admin.auto-ad.columns.longitude') }}</th>
                                        <th is='sortable' :column="'geocoding_status'">{{ trans('admin.auto-ad.columns.geocoding_status') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="48">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/auto-ads')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/auto-ads/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td>@{{ item.price }}</td>
                                        <td>@{{ item.price_contains_vat }}</td>
                                        <td>@{{ item.vin }}</td>
                                        <td>@{{ item.doors }}</td>
                                        <td>@{{ item.mileage }}</td>
                                        <td>@{{ item.exterior_color }}</td>
                                        <td>@{{ item.interior_color }}</td>
                                        <td>@{{ item.condition }}</td>
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
                                        <td>@{{ item.ad_fuel_type_id }}</td>
                                        <td>@{{ item.ad_body_type_id }}</td>
                                        <td>@{{ item.ad_transmission_type_id }}</td>
                                        <td>@{{ item.ad_drive_type_id }}</td>
                                        <td>@{{ item.first_registration_month }}</td>
                                        <td>@{{ item.first_registration_year }}</td>
                                        <td>@{{ item.engine_displacement }}</td>
                                        <td>@{{ item.power_hp }}</td>
                                        <td>@{{ item.owners }}</td>
                                        <td>@{{ item.inspection_valid_until_month }}</td>
                                        <td>@{{ item.inspection_valid_until_year }}</td>
                                        <td>@{{ item.make_id }}</td>
                                        <td>@{{ item.model_id }}</td>
                                        <td>@{{ item.generation_id }}</td>
                                        <td>@{{ item.series_id }}</td>
                                        <td>@{{ item.trim_id }}</td>
                                        <td>@{{ item.equipment_id }}</td>
                                        <td>@{{ item.additional_vehicle_info }}</td>
                                        <td>@{{ item.seats }}</td>
                                        <td>@{{ item.fuel_consumption }}</td>
                                        <td>@{{ item.co2_emissions }}</td>
                                        <td>@{{ item.latitude }}</td>
                                        <td>@{{ item.longitude }}</td>
                                        <td>@{{ item.geocoding_status }}</td>
                                        
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
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/auto-ads/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.auto-ad.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </auto-ad-listing>

@endsection