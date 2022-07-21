<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_id'), 'has-success': fields.ad_id && fields.ad_id.valid }">
    <label for="ad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.ad_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_id'), 'form-control-success': fields.ad_id && fields.ad_id.valid}" id="ad_id" name="ad_id" placeholder="{{ trans('admin.mobile-home-ad.columns.ad_id') }}">
        <div v-if="errors.has('ad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dealer_show_room_id'), 'has-success': fields.dealer_show_room_id && fields.dealer_show_room_id.valid }">
    <label for="dealer_show_room_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.dealer_show_room_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dealer_show_room_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dealer_show_room_id'), 'form-control-success': fields.dealer_show_room_id && fields.dealer_show_room_id.valid}" id="dealer_show_room_id" name="dealer_show_room_id" placeholder="{{ trans('admin.mobile-home-ad.columns.dealer_show_room_id') }}">
        <div v-if="errors.has('dealer_show_room_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dealer_show_room_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('emission_class'), 'has-success': fields.emission_class && fields.emission_class.valid }">
    <label for="emission_class" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.emission_class') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.emission_class" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('emission_class'), 'form-control-success': fields.emission_class && fields.emission_class.valid}" id="emission_class" name="emission_class" placeholder="{{ trans('admin.mobile-home-ad.columns.emission_class') }}">
        <div v-if="errors.has('emission_class')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('emission_class') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('fuel_consumption'), 'has-success': fields.fuel_consumption && fields.fuel_consumption.valid }">
    <label for="fuel_consumption" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.fuel_consumption') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.fuel_consumption" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('fuel_consumption'), 'form-control-success': fields.fuel_consumption && fields.fuel_consumption.valid}" id="fuel_consumption" name="fuel_consumption" placeholder="{{ trans('admin.mobile-home-ad.columns.fuel_consumption') }}">
        <div v-if="errors.has('fuel_consumption')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('fuel_consumption') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('co2_emissions'), 'has-success': fields.co2_emissions && fields.co2_emissions.valid }">
    <label for="co2_emissions" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.co2_emissions') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.co2_emissions" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('co2_emissions'), 'form-control-success': fields.co2_emissions && fields.co2_emissions.valid}" id="co2_emissions" name="co2_emissions" placeholder="{{ trans('admin.mobile-home-ad.columns.co2_emissions') }}">
        <div v-if="errors.has('co2_emissions')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('co2_emissions') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('condition'), 'has-success': fields.condition && fields.condition.valid }">
    <label for="condition" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.condition') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.condition" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('condition'), 'form-control-success': fields.condition && fields.condition.valid}" id="condition" name="condition" placeholder="{{ trans('admin.mobile-home-ad.columns.condition') }}">
        <div v-if="errors.has('condition')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('condition') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('color'), 'has-success': fields.color && fields.color.valid }">
    <label for="color" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.color') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.color" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('color'), 'form-control-success': fields.color && fields.color.valid}" id="color" name="color" placeholder="{{ trans('admin.mobile-home-ad.columns.color') }}">
        <div v-if="errors.has('color')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('color') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('price'), 'has-success': fields.price && fields.price.valid }">
    <label for="price" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.price') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.price" v-validate="'required|decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('price'), 'form-control-success': fields.price && fields.price.valid}" id="price" name="price" placeholder="{{ trans('admin.mobile-home-ad.columns.price') }}">
        <div v-if="errors.has('price')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('price') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('price_contains_vat'), 'has-success': fields.price_contains_vat && fields.price_contains_vat.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="price_contains_vat" type="checkbox" v-model="form.price_contains_vat" v-validate="''" data-vv-name="price_contains_vat"  name="price_contains_vat_fake_element">
        <label class="form-check-label" for="price_contains_vat">
            {{ trans('admin.mobile-home-ad.columns.price_contains_vat') }}
        </label>
        <input type="hidden" name="price_contains_vat" :value="form.price_contains_vat">
        <div v-if="errors.has('price_contains_vat')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('price_contains_vat') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dealer_id'), 'has-success': fields.dealer_id && fields.dealer_id.valid }">
    <label for="dealer_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.dealer_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dealer_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dealer_id'), 'form-control-success': fields.dealer_id && fields.dealer_id.valid}" id="dealer_id" name="dealer_id" placeholder="{{ trans('admin.mobile-home-ad.columns.dealer_id') }}">
        <div v-if="errors.has('dealer_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dealer_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_name'), 'has-success': fields.first_name && fields.first_name.valid }">
    <label for="first_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.first_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.first_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_name'), 'form-control-success': fields.first_name && fields.first_name.valid}" id="first_name" name="first_name" placeholder="{{ trans('admin.mobile-home-ad.columns.first_name') }}">
        <div v-if="errors.has('first_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('sleeping_places'), 'has-success': fields.sleeping_places && fields.sleeping_places.valid }">
    <label for="sleeping_places" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.sleeping_places') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.sleeping_places" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sleeping_places'), 'form-control-success': fields.sleeping_places && fields.sleeping_places.valid}" id="sleeping_places" name="sleeping_places" placeholder="{{ trans('admin.mobile-home-ad.columns.sleeping_places') }}">
        <div v-if="errors.has('sleeping_places')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sleeping_places') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('last_name'), 'has-success': fields.last_name && fields.last_name.valid }">
    <label for="last_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.last_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.last_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('last_name'), 'form-control-success': fields.last_name && fields.last_name.valid}" id="last_name" name="last_name" placeholder="{{ trans('admin.mobile-home-ad.columns.last_name') }}">
        <div v-if="errors.has('last_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('last_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('email_address'), 'has-success': fields.email_address && fields.email_address.valid }">
    <label for="email_address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.email_address') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.email_address" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('email_address'), 'form-control-success': fields.email_address && fields.email_address.valid}" id="email_address" name="email_address" placeholder="{{ trans('admin.mobile-home-ad.columns.email_address') }}">
        <div v-if="errors.has('email_address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email_address') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('address'), 'has-success': fields.address && fields.address.valid }">
    <label for="address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.address') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <textarea class="form-control" v-model="form.address" v-validate="'required'" id="address" name="address"></textarea>
        </div>
        <div v-if="errors.has('address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('address') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('zip_code'), 'has-success': fields.zip_code && fields.zip_code.valid }">
    <label for="zip_code" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.zip_code') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.zip_code" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('zip_code'), 'form-control-success': fields.zip_code && fields.zip_code.valid}" id="zip_code" name="zip_code" placeholder="{{ trans('admin.mobile-home-ad.columns.zip_code') }}">
        <div v-if="errors.has('zip_code')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('zip_code') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('city'), 'has-success': fields.city && fields.city.valid }">
    <label for="city" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.city') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.city" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('city'), 'form-control-success': fields.city && fields.city.valid}" id="city" name="city" placeholder="{{ trans('admin.mobile-home-ad.columns.city') }}">
        <div v-if="errors.has('city')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('city') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('country'), 'has-success': fields.country && fields.country.valid }">
    <label for="country" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.country') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.country" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('country'), 'form-control-success': fields.country && fields.country.valid}" id="country" name="country" placeholder="{{ trans('admin.mobile-home-ad.columns.country') }}">
        <div v-if="errors.has('country')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mobile_number'), 'has-success': fields.mobile_number && fields.mobile_number.valid }">
    <label for="mobile_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.mobile_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mobile_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mobile_number'), 'form-control-success': fields.mobile_number && fields.mobile_number.valid}" id="mobile_number" name="mobile_number" placeholder="{{ trans('admin.mobile-home-ad.columns.mobile_number') }}">
        <div v-if="errors.has('mobile_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mobile_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('landline_number'), 'has-success': fields.landline_number && fields.landline_number.valid }">
    <label for="landline_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.landline_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.landline_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('landline_number'), 'form-control-success': fields.landline_number && fields.landline_number.valid}" id="landline_number" name="landline_number" placeholder="{{ trans('admin.mobile-home-ad.columns.landline_number') }}">
        <div v-if="errors.has('landline_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('landline_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('whatsapp_number'), 'has-success': fields.whatsapp_number && fields.whatsapp_number.valid }">
    <label for="whatsapp_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.whatsapp_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.whatsapp_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('whatsapp_number'), 'form-control-success': fields.whatsapp_number && fields.whatsapp_number.valid}" id="whatsapp_number" name="whatsapp_number" placeholder="{{ trans('admin.mobile-home-ad.columns.whatsapp_number') }}">
        <div v-if="errors.has('whatsapp_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('whatsapp_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('beds'), 'has-success': fields.beds && fields.beds.valid }">
    <label for="beds" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.beds') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.beds" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('beds'), 'form-control-success': fields.beds && fields.beds.valid}" id="beds" name="beds" placeholder="{{ trans('admin.mobile-home-ad.columns.beds') }}">
        <div v-if="errors.has('beds')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('beds') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('seats'), 'has-success': fields.seats && fields.seats.valid }">
    <label for="seats" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.seats') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.seats" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('seats'), 'form-control-success': fields.seats && fields.seats.valid}" id="seats" name="seats" placeholder="{{ trans('admin.mobile-home-ad.columns.seats') }}">
        <div v-if="errors.has('seats')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('seats') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('make_id'), 'has-success': fields.make_id && fields.make_id.valid }">
    <label for="make_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.make_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.make_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('make_id'), 'form-control-success': fields.make_id && fields.make_id.valid}" id="make_id" name="make_id" placeholder="{{ trans('admin.mobile-home-ad.columns.make_id') }}">
        <div v-if="errors.has('make_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('make_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('inspection_valid_until_month'), 'has-success': fields.inspection_valid_until_month && fields.inspection_valid_until_month.valid }">
    <label for="inspection_valid_until_month" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.inspection_valid_until_month') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.inspection_valid_until_month" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('inspection_valid_until_month'), 'form-control-success': fields.inspection_valid_until_month && fields.inspection_valid_until_month.valid}" id="inspection_valid_until_month" name="inspection_valid_until_month" placeholder="{{ trans('admin.mobile-home-ad.columns.inspection_valid_until_month') }}">
        <div v-if="errors.has('inspection_valid_until_month')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('inspection_valid_until_month') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('custom_make'), 'has-success': fields.custom_make && fields.custom_make.valid }">
    <label for="custom_make" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.custom_make') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.custom_make" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('custom_make'), 'form-control-success': fields.custom_make && fields.custom_make.valid}" id="custom_make" name="custom_make" placeholder="{{ trans('admin.mobile-home-ad.columns.custom_make') }}">
        <div v-if="errors.has('custom_make')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('custom_make') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('model_id'), 'has-success': fields.model_id && fields.model_id.valid }">
    <label for="model_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.model_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.model_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('model_id'), 'form-control-success': fields.model_id && fields.model_id.valid}" id="model_id" name="model_id" placeholder="{{ trans('admin.mobile-home-ad.columns.model_id') }}">
        <div v-if="errors.has('model_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('model_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('custom_model'), 'has-success': fields.custom_model && fields.custom_model.valid }">
    <label for="custom_model" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.custom_model') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.custom_model" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('custom_model'), 'form-control-success': fields.custom_model && fields.custom_model.valid}" id="custom_model" name="custom_model" placeholder="{{ trans('admin.mobile-home-ad.columns.custom_model') }}">
        <div v-if="errors.has('custom_model')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('custom_model') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('fuel_type_id'), 'has-success': fields.fuel_type_id && fields.fuel_type_id.valid }">
    <label for="fuel_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.fuel_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.fuel_type_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('fuel_type_id'), 'form-control-success': fields.fuel_type_id && fields.fuel_type_id.valid}" id="fuel_type_id" name="fuel_type_id" placeholder="{{ trans('admin.mobile-home-ad.columns.fuel_type_id') }}">
        <div v-if="errors.has('fuel_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('fuel_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('vehicle_category_id'), 'has-success': fields.vehicle_category_id && fields.vehicle_category_id.valid }">
    <label for="vehicle_category_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.vehicle_category_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.vehicle_category_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('vehicle_category_id'), 'form-control-success': fields.vehicle_category_id && fields.vehicle_category_id.valid}" id="vehicle_category_id" name="vehicle_category_id" placeholder="{{ trans('admin.mobile-home-ad.columns.vehicle_category_id') }}">
        <div v-if="errors.has('vehicle_category_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('vehicle_category_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('transmission_type_id'), 'has-success': fields.transmission_type_id && fields.transmission_type_id.valid }">
    <label for="transmission_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.transmission_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.transmission_type_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('transmission_type_id'), 'form-control-success': fields.transmission_type_id && fields.transmission_type_id.valid}" id="transmission_type_id" name="transmission_type_id" placeholder="{{ trans('admin.mobile-home-ad.columns.transmission_type_id') }}">
        <div v-if="errors.has('transmission_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('transmission_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('construction_year'), 'has-success': fields.construction_year && fields.construction_year.valid }">
    <label for="construction_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.construction_year') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.construction_year" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('construction_year'), 'form-control-success': fields.construction_year && fields.construction_year.valid}" id="construction_year" name="construction_year" placeholder="{{ trans('admin.mobile-home-ad.columns.construction_year') }}">
        <div v-if="errors.has('construction_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('construction_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_registration_month'), 'has-success': fields.first_registration_month && fields.first_registration_month.valid }">
    <label for="first_registration_month" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.first_registration_month') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.first_registration_month" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_registration_month'), 'form-control-success': fields.first_registration_month && fields.first_registration_month.valid}" id="first_registration_month" name="first_registration_month" placeholder="{{ trans('admin.mobile-home-ad.columns.first_registration_month') }}">
        <div v-if="errors.has('first_registration_month')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_registration_month') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_registration_year'), 'has-success': fields.first_registration_year && fields.first_registration_year.valid }">
    <label for="first_registration_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.first_registration_year') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.first_registration_year" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_registration_year'), 'form-control-success': fields.first_registration_year && fields.first_registration_year.valid}" id="first_registration_year" name="first_registration_year" placeholder="{{ trans('admin.mobile-home-ad.columns.first_registration_year') }}">
        <div v-if="errors.has('first_registration_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_registration_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('inspection_valid_until_year'), 'has-success': fields.inspection_valid_until_year && fields.inspection_valid_until_year.valid }">
    <label for="inspection_valid_until_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.inspection_valid_until_year') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.inspection_valid_until_year" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('inspection_valid_until_year'), 'form-control-success': fields.inspection_valid_until_year && fields.inspection_valid_until_year.valid}" id="inspection_valid_until_year" name="inspection_valid_until_year" placeholder="{{ trans('admin.mobile-home-ad.columns.inspection_valid_until_year') }}">
        <div v-if="errors.has('inspection_valid_until_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('inspection_valid_until_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('axes'), 'has-success': fields.axes && fields.axes.valid }">
    <label for="axes" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.axes') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.axes" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('axes'), 'form-control-success': fields.axes && fields.axes.valid}" id="axes" name="axes" placeholder="{{ trans('admin.mobile-home-ad.columns.axes') }}">
        <div v-if="errors.has('axes')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('axes') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('owners'), 'has-success': fields.owners && fields.owners.valid }">
    <label for="owners" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.owners') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.owners" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('owners'), 'form-control-success': fields.owners && fields.owners.valid}" id="owners" name="owners" placeholder="{{ trans('admin.mobile-home-ad.columns.owners') }}">
        <div v-if="errors.has('owners')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('owners') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('length_cm'), 'has-success': fields.length_cm && fields.length_cm.valid }">
    <label for="length_cm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.length_cm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.length_cm" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('length_cm'), 'form-control-success': fields.length_cm && fields.length_cm.valid}" id="length_cm" name="length_cm" placeholder="{{ trans('admin.mobile-home-ad.columns.length_cm') }}">
        <div v-if="errors.has('length_cm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('length_cm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('width_cm'), 'has-success': fields.width_cm && fields.width_cm.valid }">
    <label for="width_cm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.width_cm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.width_cm" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('width_cm'), 'form-control-success': fields.width_cm && fields.width_cm.valid}" id="width_cm" name="width_cm" placeholder="{{ trans('admin.mobile-home-ad.columns.width_cm') }}">
        <div v-if="errors.has('width_cm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('width_cm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('height_cm'), 'has-success': fields.height_cm && fields.height_cm.valid }">
    <label for="height_cm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.height_cm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.height_cm" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('height_cm'), 'form-control-success': fields.height_cm && fields.height_cm.valid}" id="height_cm" name="height_cm" placeholder="{{ trans('admin.mobile-home-ad.columns.height_cm') }}">
        <div v-if="errors.has('height_cm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('height_cm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('max_weight_allowed_kg'), 'has-success': fields.max_weight_allowed_kg && fields.max_weight_allowed_kg.valid }">
    <label for="max_weight_allowed_kg" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.max_weight_allowed_kg') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.max_weight_allowed_kg" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('max_weight_allowed_kg'), 'form-control-success': fields.max_weight_allowed_kg && fields.max_weight_allowed_kg.valid}" id="max_weight_allowed_kg" name="max_weight_allowed_kg" placeholder="{{ trans('admin.mobile-home-ad.columns.max_weight_allowed_kg') }}">
        <div v-if="errors.has('max_weight_allowed_kg')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('max_weight_allowed_kg') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('payload_kg'), 'has-success': fields.payload_kg && fields.payload_kg.valid }">
    <label for="payload_kg" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.payload_kg') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.payload_kg" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('payload_kg'), 'form-control-success': fields.payload_kg && fields.payload_kg.valid}" id="payload_kg" name="payload_kg" placeholder="{{ trans('admin.mobile-home-ad.columns.payload_kg') }}">
        <div v-if="errors.has('payload_kg')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('payload_kg') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('engine_displacement'), 'has-success': fields.engine_displacement && fields.engine_displacement.valid }">
    <label for="engine_displacement" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.engine_displacement') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.engine_displacement" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('engine_displacement'), 'form-control-success': fields.engine_displacement && fields.engine_displacement.valid}" id="engine_displacement" name="engine_displacement" placeholder="{{ trans('admin.mobile-home-ad.columns.engine_displacement') }}">
        <div v-if="errors.has('engine_displacement')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('engine_displacement') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mileage'), 'has-success': fields.mileage && fields.mileage.valid }">
    <label for="mileage" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.mileage') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mileage" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mileage'), 'form-control-success': fields.mileage && fields.mileage.valid}" id="mileage" name="mileage" placeholder="{{ trans('admin.mobile-home-ad.columns.mileage') }}">
        <div v-if="errors.has('mileage')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mileage') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('power_kw'), 'has-success': fields.power_kw && fields.power_kw.valid }">
    <label for="power_kw" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.power_kw') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.power_kw" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('power_kw'), 'form-control-success': fields.power_kw && fields.power_kw.valid}" id="power_kw" name="power_kw" placeholder="{{ trans('admin.mobile-home-ad.columns.power_kw') }}">
        <div v-if="errors.has('power_kw')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('power_kw') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('youtube_link'), 'has-success': fields.youtube_link && fields.youtube_link.valid }">
    <label for="youtube_link" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad.columns.youtube_link') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.youtube_link" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('youtube_link'), 'form-control-success': fields.youtube_link && fields.youtube_link.valid}" id="youtube_link" name="youtube_link" placeholder="{{ trans('admin.mobile-home-ad.columns.youtube_link') }}">
        <div v-if="errors.has('youtube_link')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('youtube_link') }}</div>
    </div>
</div>


