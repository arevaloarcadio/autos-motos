<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_id'), 'has-success': fields.ad_id && fields.ad_id.valid }">
    <label for="ad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.ad_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_id'), 'form-control-success': fields.ad_id && fields.ad_id.valid}" id="ad_id" name="ad_id" placeholder="{{ trans('admin.truck-ad.columns.ad_id') }}">
        <div v-if="errors.has('ad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('price'), 'has-success': fields.price && fields.price.valid }">
    <label for="price" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.price') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.price" v-validate="'required|decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('price'), 'form-control-success': fields.price && fields.price.valid}" id="price" name="price" placeholder="{{ trans('admin.truck-ad.columns.price') }}">
        <div v-if="errors.has('price')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('price') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('wheel_formula'), 'has-success': fields.wheel_formula && fields.wheel_formula.valid }">
    <label for="wheel_formula" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.wheel_formula') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.wheel_formula" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('wheel_formula'), 'form-control-success': fields.wheel_formula && fields.wheel_formula.valid}" id="wheel_formula" name="wheel_formula" placeholder="{{ trans('admin.truck-ad.columns.wheel_formula') }}">
        <div v-if="errors.has('wheel_formula')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('wheel_formula') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('hydraulic_system'), 'has-success': fields.hydraulic_system && fields.hydraulic_system.valid }">
    <label for="hydraulic_system" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.hydraulic_system') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.hydraulic_system" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('hydraulic_system'), 'form-control-success': fields.hydraulic_system && fields.hydraulic_system.valid}" id="hydraulic_system" name="hydraulic_system" placeholder="{{ trans('admin.truck-ad.columns.hydraulic_system') }}">
        <div v-if="errors.has('hydraulic_system')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('hydraulic_system') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('seats'), 'has-success': fields.seats && fields.seats.valid }">
    <label for="seats" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.seats') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.seats" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('seats'), 'form-control-success': fields.seats && fields.seats.valid}" id="seats" name="seats" placeholder="{{ trans('admin.truck-ad.columns.seats') }}">
        <div v-if="errors.has('seats')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('seats') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mileage'), 'has-success': fields.mileage && fields.mileage.valid }">
    <label for="mileage" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.mileage') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mileage" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mileage'), 'form-control-success': fields.mileage && fields.mileage.valid}" id="mileage" name="mileage" placeholder="{{ trans('admin.truck-ad.columns.mileage') }}">
        <div v-if="errors.has('mileage')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mileage') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('power_kw'), 'has-success': fields.power_kw && fields.power_kw.valid }">
    <label for="power_kw" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.power_kw') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.power_kw" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('power_kw'), 'form-control-success': fields.power_kw && fields.power_kw.valid}" id="power_kw" name="power_kw" placeholder="{{ trans('admin.truck-ad.columns.power_kw') }}">
        <div v-if="errors.has('power_kw')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('power_kw') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('emission_class'), 'has-success': fields.emission_class && fields.emission_class.valid }">
    <label for="emission_class" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.emission_class') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.emission_class" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('emission_class'), 'form-control-success': fields.emission_class && fields.emission_class.valid}" id="emission_class" name="emission_class" placeholder="{{ trans('admin.truck-ad.columns.emission_class') }}">
        <div v-if="errors.has('emission_class')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('emission_class') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('fuel_consumption'), 'has-success': fields.fuel_consumption && fields.fuel_consumption.valid }">
    <label for="fuel_consumption" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.fuel_consumption') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.fuel_consumption" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('fuel_consumption'), 'form-control-success': fields.fuel_consumption && fields.fuel_consumption.valid}" id="fuel_consumption" name="fuel_consumption" placeholder="{{ trans('admin.truck-ad.columns.fuel_consumption') }}">
        <div v-if="errors.has('fuel_consumption')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('fuel_consumption') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('co2_emissions'), 'has-success': fields.co2_emissions && fields.co2_emissions.valid }">
    <label for="co2_emissions" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.co2_emissions') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.co2_emissions" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('co2_emissions'), 'form-control-success': fields.co2_emissions && fields.co2_emissions.valid}" id="co2_emissions" name="co2_emissions" placeholder="{{ trans('admin.truck-ad.columns.co2_emissions') }}">
        <div v-if="errors.has('co2_emissions')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('co2_emissions') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('condition'), 'has-success': fields.condition && fields.condition.valid }">
    <label for="condition" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.condition') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.condition" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('condition'), 'form-control-success': fields.condition && fields.condition.valid}" id="condition" name="condition" placeholder="{{ trans('admin.truck-ad.columns.condition') }}">
        <div v-if="errors.has('condition')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('condition') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('interior_color'), 'has-success': fields.interior_color && fields.interior_color.valid }">
    <label for="interior_color" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.interior_color') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.interior_color" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('interior_color'), 'form-control-success': fields.interior_color && fields.interior_color.valid}" id="interior_color" name="interior_color" placeholder="{{ trans('admin.truck-ad.columns.interior_color') }}">
        <div v-if="errors.has('interior_color')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('interior_color') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('exterior_color'), 'has-success': fields.exterior_color && fields.exterior_color.valid }">
    <label for="exterior_color" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.exterior_color') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.exterior_color" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('exterior_color'), 'form-control-success': fields.exterior_color && fields.exterior_color.valid}" id="exterior_color" name="exterior_color" placeholder="{{ trans('admin.truck-ad.columns.exterior_color') }}">
        <div v-if="errors.has('exterior_color')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('exterior_color') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('price_contains_vat'), 'has-success': fields.price_contains_vat && fields.price_contains_vat.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="price_contains_vat" type="checkbox" v-model="form.price_contains_vat" v-validate="''" data-vv-name="price_contains_vat"  name="price_contains_vat_fake_element">
        <label class="form-check-label" for="price_contains_vat">
            {{ trans('admin.truck-ad.columns.price_contains_vat') }}
        </label>
        <input type="hidden" name="price_contains_vat" :value="form.price_contains_vat">
        <div v-if="errors.has('price_contains_vat')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('price_contains_vat') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('operating_hours'), 'has-success': fields.operating_hours && fields.operating_hours.valid }">
    <label for="operating_hours" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.operating_hours') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.operating_hours" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('operating_hours'), 'form-control-success': fields.operating_hours && fields.operating_hours.valid}" id="operating_hours" name="operating_hours" placeholder="{{ trans('admin.truck-ad.columns.operating_hours') }}">
        <div v-if="errors.has('operating_hours')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('operating_hours') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dealer_id'), 'has-success': fields.dealer_id && fields.dealer_id.valid }">
    <label for="dealer_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.dealer_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dealer_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dealer_id'), 'form-control-success': fields.dealer_id && fields.dealer_id.valid}" id="dealer_id" name="dealer_id" placeholder="{{ trans('admin.truck-ad.columns.dealer_id') }}">
        <div v-if="errors.has('dealer_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dealer_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dealer_show_room_id'), 'has-success': fields.dealer_show_room_id && fields.dealer_show_room_id.valid }">
    <label for="dealer_show_room_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.dealer_show_room_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dealer_show_room_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dealer_show_room_id'), 'form-control-success': fields.dealer_show_room_id && fields.dealer_show_room_id.valid}" id="dealer_show_room_id" name="dealer_show_room_id" placeholder="{{ trans('admin.truck-ad.columns.dealer_show_room_id') }}">
        <div v-if="errors.has('dealer_show_room_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dealer_show_room_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_name'), 'has-success': fields.first_name && fields.first_name.valid }">
    <label for="first_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.first_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.first_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_name'), 'form-control-success': fields.first_name && fields.first_name.valid}" id="first_name" name="first_name" placeholder="{{ trans('admin.truck-ad.columns.first_name') }}">
        <div v-if="errors.has('first_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('last_name'), 'has-success': fields.last_name && fields.last_name.valid }">
    <label for="last_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.last_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.last_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('last_name'), 'form-control-success': fields.last_name && fields.last_name.valid}" id="last_name" name="last_name" placeholder="{{ trans('admin.truck-ad.columns.last_name') }}">
        <div v-if="errors.has('last_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('last_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('email_address'), 'has-success': fields.email_address && fields.email_address.valid }">
    <label for="email_address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.email_address') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.email_address" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('email_address'), 'form-control-success': fields.email_address && fields.email_address.valid}" id="email_address" name="email_address" placeholder="{{ trans('admin.truck-ad.columns.email_address') }}">
        <div v-if="errors.has('email_address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email_address') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('address'), 'has-success': fields.address && fields.address.valid }">
    <label for="address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.address') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <textarea class="form-control" v-model="form.address" v-validate="'required'" id="address" name="address"></textarea>
        </div>
        <div v-if="errors.has('address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('address') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('zip_code'), 'has-success': fields.zip_code && fields.zip_code.valid }">
    <label for="zip_code" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.zip_code') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.zip_code" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('zip_code'), 'form-control-success': fields.zip_code && fields.zip_code.valid}" id="zip_code" name="zip_code" placeholder="{{ trans('admin.truck-ad.columns.zip_code') }}">
        <div v-if="errors.has('zip_code')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('zip_code') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('city'), 'has-success': fields.city && fields.city.valid }">
    <label for="city" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.city') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.city" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('city'), 'form-control-success': fields.city && fields.city.valid}" id="city" name="city" placeholder="{{ trans('admin.truck-ad.columns.city') }}">
        <div v-if="errors.has('city')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('city') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('country'), 'has-success': fields.country && fields.country.valid }">
    <label for="country" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.country') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.country" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('country'), 'form-control-success': fields.country && fields.country.valid}" id="country" name="country" placeholder="{{ trans('admin.truck-ad.columns.country') }}">
        <div v-if="errors.has('country')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mobile_number'), 'has-success': fields.mobile_number && fields.mobile_number.valid }">
    <label for="mobile_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.mobile_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mobile_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mobile_number'), 'form-control-success': fields.mobile_number && fields.mobile_number.valid}" id="mobile_number" name="mobile_number" placeholder="{{ trans('admin.truck-ad.columns.mobile_number') }}">
        <div v-if="errors.has('mobile_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mobile_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('landline_number'), 'has-success': fields.landline_number && fields.landline_number.valid }">
    <label for="landline_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.landline_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.landline_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('landline_number'), 'form-control-success': fields.landline_number && fields.landline_number.valid}" id="landline_number" name="landline_number" placeholder="{{ trans('admin.truck-ad.columns.landline_number') }}">
        <div v-if="errors.has('landline_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('landline_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('whatsapp_number'), 'has-success': fields.whatsapp_number && fields.whatsapp_number.valid }">
    <label for="whatsapp_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.whatsapp_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.whatsapp_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('whatsapp_number'), 'form-control-success': fields.whatsapp_number && fields.whatsapp_number.valid}" id="whatsapp_number" name="whatsapp_number" placeholder="{{ trans('admin.truck-ad.columns.whatsapp_number') }}">
        <div v-if="errors.has('whatsapp_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('whatsapp_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('axes'), 'has-success': fields.axes && fields.axes.valid }">
    <label for="axes" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.axes') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.axes" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('axes'), 'form-control-success': fields.axes && fields.axes.valid}" id="axes" name="axes" placeholder="{{ trans('admin.truck-ad.columns.axes') }}">
        <div v-if="errors.has('axes')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('axes') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('operating_weight_kg'), 'has-success': fields.operating_weight_kg && fields.operating_weight_kg.valid }">
    <label for="operating_weight_kg" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.operating_weight_kg') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.operating_weight_kg" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('operating_weight_kg'), 'form-control-success': fields.operating_weight_kg && fields.operating_weight_kg.valid}" id="operating_weight_kg" name="operating_weight_kg" placeholder="{{ trans('admin.truck-ad.columns.operating_weight_kg') }}">
        <div v-if="errors.has('operating_weight_kg')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('operating_weight_kg') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('make_id'), 'has-success': fields.make_id && fields.make_id.valid }">
    <label for="make_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.make_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.make_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('make_id'), 'form-control-success': fields.make_id && fields.make_id.valid}" id="make_id" name="make_id" placeholder="{{ trans('admin.truck-ad.columns.make_id') }}">
        <div v-if="errors.has('make_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('make_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('inspection_valid_until_year'), 'has-success': fields.inspection_valid_until_year && fields.inspection_valid_until_year.valid }">
    <label for="inspection_valid_until_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.inspection_valid_until_year') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.inspection_valid_until_year" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('inspection_valid_until_year'), 'form-control-success': fields.inspection_valid_until_year && fields.inspection_valid_until_year.valid}" id="inspection_valid_until_year" name="inspection_valid_until_year" placeholder="{{ trans('admin.truck-ad.columns.inspection_valid_until_year') }}">
        <div v-if="errors.has('inspection_valid_until_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('inspection_valid_until_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('custom_make'), 'has-success': fields.custom_make && fields.custom_make.valid }">
    <label for="custom_make" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.custom_make') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.custom_make" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('custom_make'), 'form-control-success': fields.custom_make && fields.custom_make.valid}" id="custom_make" name="custom_make" placeholder="{{ trans('admin.truck-ad.columns.custom_make') }}">
        <div v-if="errors.has('custom_make')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('custom_make') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('model'), 'has-success': fields.model && fields.model.valid }">
    <label for="model" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.model') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.model" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('model'), 'form-control-success': fields.model && fields.model.valid}" id="model" name="model" placeholder="{{ trans('admin.truck-ad.columns.model') }}">
        <div v-if="errors.has('model')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('model') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('truck_type'), 'has-success': fields.truck_type && fields.truck_type.valid }">
    <label for="truck_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.truck_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.truck_type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('truck_type'), 'form-control-success': fields.truck_type && fields.truck_type.valid}" id="truck_type" name="truck_type" placeholder="{{ trans('admin.truck-ad.columns.truck_type') }}">
        <div v-if="errors.has('truck_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('truck_type') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('fuel_type_id'), 'has-success': fields.fuel_type_id && fields.fuel_type_id.valid }">
    <label for="fuel_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.fuel_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.fuel_type_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('fuel_type_id'), 'form-control-success': fields.fuel_type_id && fields.fuel_type_id.valid}" id="fuel_type_id" name="fuel_type_id" placeholder="{{ trans('admin.truck-ad.columns.fuel_type_id') }}">
        <div v-if="errors.has('fuel_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('fuel_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('vehicle_category_id'), 'has-success': fields.vehicle_category_id && fields.vehicle_category_id.valid }">
    <label for="vehicle_category_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.vehicle_category_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.vehicle_category_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('vehicle_category_id'), 'form-control-success': fields.vehicle_category_id && fields.vehicle_category_id.valid}" id="vehicle_category_id" name="vehicle_category_id" placeholder="{{ trans('admin.truck-ad.columns.vehicle_category_id') }}">
        <div v-if="errors.has('vehicle_category_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('vehicle_category_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('transmission_type_id'), 'has-success': fields.transmission_type_id && fields.transmission_type_id.valid }">
    <label for="transmission_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.transmission_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.transmission_type_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('transmission_type_id'), 'form-control-success': fields.transmission_type_id && fields.transmission_type_id.valid}" id="transmission_type_id" name="transmission_type_id" placeholder="{{ trans('admin.truck-ad.columns.transmission_type_id') }}">
        <div v-if="errors.has('transmission_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('transmission_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('cab'), 'has-success': fields.cab && fields.cab.valid }">
    <label for="cab" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.cab') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.cab" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('cab'), 'form-control-success': fields.cab && fields.cab.valid}" id="cab" name="cab" placeholder="{{ trans('admin.truck-ad.columns.cab') }}">
        <div v-if="errors.has('cab')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cab') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('construction_year'), 'has-success': fields.construction_year && fields.construction_year.valid }">
    <label for="construction_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.construction_year') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.construction_year" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('construction_year'), 'form-control-success': fields.construction_year && fields.construction_year.valid}" id="construction_year" name="construction_year" placeholder="{{ trans('admin.truck-ad.columns.construction_year') }}">
        <div v-if="errors.has('construction_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('construction_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_registration_month'), 'has-success': fields.first_registration_month && fields.first_registration_month.valid }">
    <label for="first_registration_month" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.first_registration_month') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.first_registration_month" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_registration_month'), 'form-control-success': fields.first_registration_month && fields.first_registration_month.valid}" id="first_registration_month" name="first_registration_month" placeholder="{{ trans('admin.truck-ad.columns.first_registration_month') }}">
        <div v-if="errors.has('first_registration_month')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_registration_month') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_registration_year'), 'has-success': fields.first_registration_year && fields.first_registration_year.valid }">
    <label for="first_registration_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.first_registration_year') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.first_registration_year" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_registration_year'), 'form-control-success': fields.first_registration_year && fields.first_registration_year.valid}" id="first_registration_year" name="first_registration_year" placeholder="{{ trans('admin.truck-ad.columns.first_registration_year') }}">
        <div v-if="errors.has('first_registration_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_registration_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('inspection_valid_until_month'), 'has-success': fields.inspection_valid_until_month && fields.inspection_valid_until_month.valid }">
    <label for="inspection_valid_until_month" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.inspection_valid_until_month') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.inspection_valid_until_month" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('inspection_valid_until_month'), 'form-control-success': fields.inspection_valid_until_month && fields.inspection_valid_until_month.valid}" id="inspection_valid_until_month" name="inspection_valid_until_month" placeholder="{{ trans('admin.truck-ad.columns.inspection_valid_until_month') }}">
        <div v-if="errors.has('inspection_valid_until_month')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('inspection_valid_until_month') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('owners'), 'has-success': fields.owners && fields.owners.valid }">
    <label for="owners" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.owners') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.owners" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('owners'), 'form-control-success': fields.owners && fields.owners.valid}" id="owners" name="owners" placeholder="{{ trans('admin.truck-ad.columns.owners') }}">
        <div v-if="errors.has('owners')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('owners') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('load_capacity_kg'), 'has-success': fields.load_capacity_kg && fields.load_capacity_kg.valid }">
    <label for="load_capacity_kg" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.load_capacity_kg') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.load_capacity_kg" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('load_capacity_kg'), 'form-control-success': fields.load_capacity_kg && fields.load_capacity_kg.valid}" id="load_capacity_kg" name="load_capacity_kg" placeholder="{{ trans('admin.truck-ad.columns.load_capacity_kg') }}">
        <div v-if="errors.has('load_capacity_kg')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('load_capacity_kg') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('construction_height_mm'), 'has-success': fields.construction_height_mm && fields.construction_height_mm.valid }">
    <label for="construction_height_mm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.construction_height_mm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.construction_height_mm" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('construction_height_mm'), 'form-control-success': fields.construction_height_mm && fields.construction_height_mm.valid}" id="construction_height_mm" name="construction_height_mm" placeholder="{{ trans('admin.truck-ad.columns.construction_height_mm') }}">
        <div v-if="errors.has('construction_height_mm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('construction_height_mm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('lifting_height_mm'), 'has-success': fields.lifting_height_mm && fields.lifting_height_mm.valid }">
    <label for="lifting_height_mm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.lifting_height_mm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.lifting_height_mm" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('lifting_height_mm'), 'form-control-success': fields.lifting_height_mm && fields.lifting_height_mm.valid}" id="lifting_height_mm" name="lifting_height_mm" placeholder="{{ trans('admin.truck-ad.columns.lifting_height_mm') }}">
        <div v-if="errors.has('lifting_height_mm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('lifting_height_mm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('lifting_capacity_kg_m'), 'has-success': fields.lifting_capacity_kg_m && fields.lifting_capacity_kg_m.valid }">
    <label for="lifting_capacity_kg_m" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.lifting_capacity_kg_m') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.lifting_capacity_kg_m" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('lifting_capacity_kg_m'), 'form-control-success': fields.lifting_capacity_kg_m && fields.lifting_capacity_kg_m.valid}" id="lifting_capacity_kg_m" name="lifting_capacity_kg_m" placeholder="{{ trans('admin.truck-ad.columns.lifting_capacity_kg_m') }}">
        <div v-if="errors.has('lifting_capacity_kg_m')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('lifting_capacity_kg_m') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('permanent_total_weight_kg'), 'has-success': fields.permanent_total_weight_kg && fields.permanent_total_weight_kg.valid }">
    <label for="permanent_total_weight_kg" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.permanent_total_weight_kg') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.permanent_total_weight_kg" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('permanent_total_weight_kg'), 'form-control-success': fields.permanent_total_weight_kg && fields.permanent_total_weight_kg.valid}" id="permanent_total_weight_kg" name="permanent_total_weight_kg" placeholder="{{ trans('admin.truck-ad.columns.permanent_total_weight_kg') }}">
        <div v-if="errors.has('permanent_total_weight_kg')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('permanent_total_weight_kg') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('allowed_pulling_weight_kg'), 'has-success': fields.allowed_pulling_weight_kg && fields.allowed_pulling_weight_kg.valid }">
    <label for="allowed_pulling_weight_kg" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.allowed_pulling_weight_kg') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.allowed_pulling_weight_kg" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('allowed_pulling_weight_kg'), 'form-control-success': fields.allowed_pulling_weight_kg && fields.allowed_pulling_weight_kg.valid}" id="allowed_pulling_weight_kg" name="allowed_pulling_weight_kg" placeholder="{{ trans('admin.truck-ad.columns.allowed_pulling_weight_kg') }}">
        <div v-if="errors.has('allowed_pulling_weight_kg')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('allowed_pulling_weight_kg') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('payload_kg'), 'has-success': fields.payload_kg && fields.payload_kg.valid }">
    <label for="payload_kg" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.payload_kg') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.payload_kg" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('payload_kg'), 'form-control-success': fields.payload_kg && fields.payload_kg.valid}" id="payload_kg" name="payload_kg" placeholder="{{ trans('admin.truck-ad.columns.payload_kg') }}">
        <div v-if="errors.has('payload_kg')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('payload_kg') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('max_weight_allowed_kg'), 'has-success': fields.max_weight_allowed_kg && fields.max_weight_allowed_kg.valid }">
    <label for="max_weight_allowed_kg" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.max_weight_allowed_kg') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.max_weight_allowed_kg" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('max_weight_allowed_kg'), 'form-control-success': fields.max_weight_allowed_kg && fields.max_weight_allowed_kg.valid}" id="max_weight_allowed_kg" name="max_weight_allowed_kg" placeholder="{{ trans('admin.truck-ad.columns.max_weight_allowed_kg') }}">
        <div v-if="errors.has('max_weight_allowed_kg')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('max_weight_allowed_kg') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('empty_weight_kg'), 'has-success': fields.empty_weight_kg && fields.empty_weight_kg.valid }">
    <label for="empty_weight_kg" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.empty_weight_kg') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.empty_weight_kg" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('empty_weight_kg'), 'form-control-success': fields.empty_weight_kg && fields.empty_weight_kg.valid}" id="empty_weight_kg" name="empty_weight_kg" placeholder="{{ trans('admin.truck-ad.columns.empty_weight_kg') }}">
        <div v-if="errors.has('empty_weight_kg')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('empty_weight_kg') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('loading_space_length_mm'), 'has-success': fields.loading_space_length_mm && fields.loading_space_length_mm.valid }">
    <label for="loading_space_length_mm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.loading_space_length_mm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.loading_space_length_mm" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('loading_space_length_mm'), 'form-control-success': fields.loading_space_length_mm && fields.loading_space_length_mm.valid}" id="loading_space_length_mm" name="loading_space_length_mm" placeholder="{{ trans('admin.truck-ad.columns.loading_space_length_mm') }}">
        <div v-if="errors.has('loading_space_length_mm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('loading_space_length_mm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('loading_space_width_mm'), 'has-success': fields.loading_space_width_mm && fields.loading_space_width_mm.valid }">
    <label for="loading_space_width_mm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.loading_space_width_mm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.loading_space_width_mm" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('loading_space_width_mm'), 'form-control-success': fields.loading_space_width_mm && fields.loading_space_width_mm.valid}" id="loading_space_width_mm" name="loading_space_width_mm" placeholder="{{ trans('admin.truck-ad.columns.loading_space_width_mm') }}">
        <div v-if="errors.has('loading_space_width_mm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('loading_space_width_mm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('loading_space_height_mm'), 'has-success': fields.loading_space_height_mm && fields.loading_space_height_mm.valid }">
    <label for="loading_space_height_mm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.loading_space_height_mm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.loading_space_height_mm" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('loading_space_height_mm'), 'form-control-success': fields.loading_space_height_mm && fields.loading_space_height_mm.valid}" id="loading_space_height_mm" name="loading_space_height_mm" placeholder="{{ trans('admin.truck-ad.columns.loading_space_height_mm') }}">
        <div v-if="errors.has('loading_space_height_mm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('loading_space_height_mm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('loading_volume_m3'), 'has-success': fields.loading_volume_m3 && fields.loading_volume_m3.valid }">
    <label for="loading_volume_m3" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.loading_volume_m3') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.loading_volume_m3" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('loading_volume_m3'), 'form-control-success': fields.loading_volume_m3 && fields.loading_volume_m3.valid}" id="loading_volume_m3" name="loading_volume_m3" placeholder="{{ trans('admin.truck-ad.columns.loading_volume_m3') }}">
        <div v-if="errors.has('loading_volume_m3')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('loading_volume_m3') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('youtube_link'), 'has-success': fields.youtube_link && fields.youtube_link.valid }">
    <label for="youtube_link" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.truck-ad.columns.youtube_link') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.youtube_link" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('youtube_link'), 'form-control-success': fields.youtube_link && fields.youtube_link.valid}" id="youtube_link" name="youtube_link" placeholder="{{ trans('admin.truck-ad.columns.youtube_link') }}">
        <div v-if="errors.has('youtube_link')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('youtube_link') }}</div>
    </div>
</div>


