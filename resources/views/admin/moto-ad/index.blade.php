@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.moto-ad.actions.index'))

@section('body')

    <moto-ad-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/moto-ads') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.moto-ad.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/moto-ads/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.moto-ad.actions.create') }}</a>
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

                                        <th is='sortable' :column="'id'">{{ trans('admin.moto-ad.columns.id') }}</th>
                                        <th is='sortable' :column="'ad_id'">{{ trans('admin.moto-ad.columns.ad_id') }}</th>
                                        <th is='sortable' :column="'make_id'">{{ trans('admin.moto-ad.columns.make_id') }}</th>
                                        <th is='sortable' :column="'custom_make'">{{ trans('admin.moto-ad.columns.custom_make') }}</th>
                                        <th is='sortable' :column="'model_id'">{{ trans('admin.moto-ad.columns.model_id') }}</th>
                                        <th is='sortable' :column="'custom_model'">{{ trans('admin.moto-ad.columns.custom_model') }}</th>
                                        <th is='sortable' :column="'fuel_type_id'">{{ trans('admin.moto-ad.columns.fuel_type_id') }}</th>
                                        <th is='sortable' :column="'body_type_id'">{{ trans('admin.moto-ad.columns.body_type_id') }}</th>
                                        <th is='sortable' :column="'transmission_type_id'">{{ trans('admin.moto-ad.columns.transmission_type_id') }}</th>
                                        <th is='sortable' :column="'drive_type_id'">{{ trans('admin.moto-ad.columns.drive_type_id') }}</th>
                                        <th is='sortable' :column="'first_registration_month'">{{ trans('admin.moto-ad.columns.first_registration_month') }}</th>
                                        <th is='sortable' :column="'first_registration_year'">{{ trans('admin.moto-ad.columns.first_registration_year') }}</th>
                                        <th is='sortable' :column="'inspection_valid_until_month'">{{ trans('admin.moto-ad.columns.inspection_valid_until_month') }}</th>
                                        <th is='sortable' :column="'inspection_valid_until_year'">{{ trans('admin.moto-ad.columns.inspection_valid_until_year') }}</th>
                                        <th is='sortable' :column="'last_customer_service_month'">{{ trans('admin.moto-ad.columns.last_customer_service_month') }}</th>
                                        <th is='sortable' :column="'last_customer_service_year'">{{ trans('admin.moto-ad.columns.last_customer_service_year') }}</th>
                                        <th is='sortable' :column="'owners'">{{ trans('admin.moto-ad.columns.owners') }}</th>
                                        <th is='sortable' :column="'weight_kg'">{{ trans('admin.moto-ad.columns.weight_kg') }}</th>
                                        <th is='sortable' :column="'engine_displacement'">{{ trans('admin.moto-ad.columns.engine_displacement') }}</th>
                                        <th is='sortable' :column="'mileage'">{{ trans('admin.moto-ad.columns.mileage') }}</th>
                                        <th is='sortable' :column="'power_kw'">{{ trans('admin.moto-ad.columns.power_kw') }}</th>
                                        <th is='sortable' :column="'gears'">{{ trans('admin.moto-ad.columns.gears') }}</th>
                                        <th is='sortable' :column="'cylinders'">{{ trans('admin.moto-ad.columns.cylinders') }}</th>
                                        <th is='sortable' :column="'emission_class'">{{ trans('admin.moto-ad.columns.emission_class') }}</th>
                                        <th is='sortable' :column="'fuel_consumption'">{{ trans('admin.moto-ad.columns.fuel_consumption') }}</th>
                                        <th is='sortable' :column="'co2_emissions'">{{ trans('admin.moto-ad.columns.co2_emissions') }}</th>
                                        <th is='sortable' :column="'condition'">{{ trans('admin.moto-ad.columns.condition') }}</th>
                                        <th is='sortable' :column="'color'">{{ trans('admin.moto-ad.columns.color') }}</th>
                                        <th is='sortable' :column="'price'">{{ trans('admin.moto-ad.columns.price') }}</th>
                                        <th is='sortable' :column="'price_contains_vat'">{{ trans('admin.moto-ad.columns.price_contains_vat') }}</th>
                                        <th is='sortable' :column="'dealer_id'">{{ trans('admin.moto-ad.columns.dealer_id') }}</th>
                                        <th is='sortable' :column="'dealer_show_room_id'">{{ trans('admin.moto-ad.columns.dealer_show_room_id') }}</th>
                                        <th is='sortable' :column="'first_name'">{{ trans('admin.moto-ad.columns.first_name') }}</th>
                                        <th is='sortable' :column="'last_name'">{{ trans('admin.moto-ad.columns.last_name') }}</th>
                                        <th is='sortable' :column="'email_address'">{{ trans('admin.moto-ad.columns.email_address') }}</th>
                                        <th is='sortable' :column="'zip_code'">{{ trans('admin.moto-ad.columns.zip_code') }}</th>
                                        <th is='sortable' :column="'city'">{{ trans('admin.moto-ad.columns.city') }}</th>
                                        <th is='sortable' :column="'country'">{{ trans('admin.moto-ad.columns.country') }}</th>
                                        <th is='sortable' :column="'mobile_number'">{{ trans('admin.moto-ad.columns.mobile_number') }}</th>
                                        <th is='sortable' :column="'landline_number'">{{ trans('admin.moto-ad.columns.landline_number') }}</th>
                                        <th is='sortable' :column="'whatsapp_number'">{{ trans('admin.moto-ad.columns.whatsapp_number') }}</th>
                                        <th is='sortable' :column="'youtube_link'">{{ trans('admin.moto-ad.columns.youtube_link') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="44">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/moto-ads')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/moto-ads/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td>@{{ item.model_id }}</td>
                                        <td>@{{ item.custom_model }}</td>
                                        <td>@{{ item.fuel_type_id }}</td>
                                        <td>@{{ item.body_type_id }}</td>
                                        <td>@{{ item.transmission_type_id }}</td>
                                        <td>@{{ item.drive_type_id }}</td>
                                        <td>@{{ item.first_registration_month }}</td>
                                        <td>@{{ item.first_registration_year }}</td>
                                        <td>@{{ item.inspection_valid_until_month }}</td>
                                        <td>@{{ item.inspection_valid_until_year }}</td>
                                        <td>@{{ item.last_customer_service_month }}</td>
                                        <td>@{{ item.last_customer_service_year }}</td>
                                        <td>@{{ item.owners }}</td>
                                        <td>@{{ item.weight_kg }}</td>
                                        <td>@{{ item.engine_displacement }}</td>
                                        <td>@{{ item.mileage }}</td>
                                        <td>@{{ item.power_kw }}</td>
                                        <td>@{{ item.gears }}</td>
                                        <td>@{{ item.cylinders }}</td>
                                        <td>@{{ item.emission_class }}</td>
                                        <td>@{{ item.fuel_consumption }}</td>
                                        <td>@{{ item.co2_emissions }}</td>
                                        <td>@{{ item.condition }}</td>
                                        <td>@{{ item.color }}</td>
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
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/moto-ads/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.moto-ad.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </moto-ad-listing>

@endsection