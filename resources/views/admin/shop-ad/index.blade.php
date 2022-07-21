@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.shop-ad.actions.index'))

@section('body')

    <shop-ad-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/shop-ads') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.shop-ad.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/shop-ads/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.shop-ad.actions.create') }}</a>
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

                                        <th is='sortable' :column="'id'">{{ trans('admin.shop-ad.columns.id') }}</th>
                                        <th is='sortable' :column="'ad_id'">{{ trans('admin.shop-ad.columns.ad_id') }}</th>
                                        <th is='sortable' :column="'category'">{{ trans('admin.shop-ad.columns.category') }}</th>
                                        <th is='sortable' :column="'make_id'">{{ trans('admin.shop-ad.columns.make_id') }}</th>
                                        <th is='sortable' :column="'model'">{{ trans('admin.shop-ad.columns.model') }}</th>
                                        <th is='sortable' :column="'manufacturer'">{{ trans('admin.shop-ad.columns.manufacturer') }}</th>
                                        <th is='sortable' :column="'code'">{{ trans('admin.shop-ad.columns.code') }}</th>
                                        <th is='sortable' :column="'condition'">{{ trans('admin.shop-ad.columns.condition') }}</th>
                                        <th is='sortable' :column="'price'">{{ trans('admin.shop-ad.columns.price') }}</th>
                                        <th is='sortable' :column="'price_contains_vat'">{{ trans('admin.shop-ad.columns.price_contains_vat') }}</th>
                                        <th is='sortable' :column="'dealer_id'">{{ trans('admin.shop-ad.columns.dealer_id') }}</th>
                                        <th is='sortable' :column="'dealer_show_room_id'">{{ trans('admin.shop-ad.columns.dealer_show_room_id') }}</th>
                                        <th is='sortable' :column="'first_name'">{{ trans('admin.shop-ad.columns.first_name') }}</th>
                                        <th is='sortable' :column="'last_name'">{{ trans('admin.shop-ad.columns.last_name') }}</th>
                                        <th is='sortable' :column="'email_address'">{{ trans('admin.shop-ad.columns.email_address') }}</th>
                                        <th is='sortable' :column="'zip_code'">{{ trans('admin.shop-ad.columns.zip_code') }}</th>
                                        <th is='sortable' :column="'city'">{{ trans('admin.shop-ad.columns.city') }}</th>
                                        <th is='sortable' :column="'country'">{{ trans('admin.shop-ad.columns.country') }}</th>
                                        <th is='sortable' :column="'latitude'">{{ trans('admin.shop-ad.columns.latitude') }}</th>
                                        <th is='sortable' :column="'longitude'">{{ trans('admin.shop-ad.columns.longitude') }}</th>
                                        <th is='sortable' :column="'mobile_number'">{{ trans('admin.shop-ad.columns.mobile_number') }}</th>
                                        <th is='sortable' :column="'landline_number'">{{ trans('admin.shop-ad.columns.landline_number') }}</th>
                                        <th is='sortable' :column="'whatsapp_number'">{{ trans('admin.shop-ad.columns.whatsapp_number') }}</th>
                                        <th is='sortable' :column="'youtube_link'">{{ trans('admin.shop-ad.columns.youtube_link') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="26">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/shop-ads')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/shop-ads/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td>@{{ item.category }}</td>
                                        <td>@{{ item.make_id }}</td>
                                        <td>@{{ item.model }}</td>
                                        <td>@{{ item.manufacturer }}</td>
                                        <td>@{{ item.code }}</td>
                                        <td>@{{ item.condition }}</td>
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
                                        <td>@{{ item.latitude }}</td>
                                        <td>@{{ item.longitude }}</td>
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
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/shop-ads/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.shop-ad.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </shop-ad-listing>

@endsection