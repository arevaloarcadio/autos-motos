<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.dealer-show-room.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('address'), 'has-success': fields.address && fields.address.valid }">
    <label for="address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.address') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.address" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('address'), 'form-control-success': fields.address && fields.address.valid}" id="address" name="address" placeholder="{{ trans('admin.dealer-show-room.columns.address') }}">
        <div v-if="errors.has('address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('address') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('zip_code'), 'has-success': fields.zip_code && fields.zip_code.valid }">
    <label for="zip_code" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.zip_code') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.zip_code" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('zip_code'), 'form-control-success': fields.zip_code && fields.zip_code.valid}" id="zip_code" name="zip_code" placeholder="{{ trans('admin.dealer-show-room.columns.zip_code') }}">
        <div v-if="errors.has('zip_code')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('zip_code') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('city'), 'has-success': fields.city && fields.city.valid }">
    <label for="city" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.city') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.city" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('city'), 'form-control-success': fields.city && fields.city.valid}" id="city" name="city" placeholder="{{ trans('admin.dealer-show-room.columns.city') }}">
        <div v-if="errors.has('city')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('city') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('country'), 'has-success': fields.country && fields.country.valid }">
    <label for="country" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.country') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.country" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('country'), 'form-control-success': fields.country && fields.country.valid}" id="country" name="country" placeholder="{{ trans('admin.dealer-show-room.columns.country') }}">
        <div v-if="errors.has('country')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('latitude'), 'has-success': fields.latitude && fields.latitude.valid }">
    <label for="latitude" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.latitude') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.latitude" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('latitude'), 'form-control-success': fields.latitude && fields.latitude.valid}" id="latitude" name="latitude" placeholder="{{ trans('admin.dealer-show-room.columns.latitude') }}">
        <div v-if="errors.has('latitude')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('latitude') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('longitude'), 'has-success': fields.longitude && fields.longitude.valid }">
    <label for="longitude" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.longitude') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.longitude" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('longitude'), 'form-control-success': fields.longitude && fields.longitude.valid}" id="longitude" name="longitude" placeholder="{{ trans('admin.dealer-show-room.columns.longitude') }}">
        <div v-if="errors.has('longitude')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('longitude') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('email_address'), 'has-success': fields.email_address && fields.email_address.valid }">
    <label for="email_address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.email_address') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.email_address" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('email_address'), 'form-control-success': fields.email_address && fields.email_address.valid}" id="email_address" name="email_address" placeholder="{{ trans('admin.dealer-show-room.columns.email_address') }}">
        <div v-if="errors.has('email_address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email_address') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mobile_number'), 'has-success': fields.mobile_number && fields.mobile_number.valid }">
    <label for="mobile_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.mobile_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mobile_number" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mobile_number'), 'form-control-success': fields.mobile_number && fields.mobile_number.valid}" id="mobile_number" name="mobile_number" placeholder="{{ trans('admin.dealer-show-room.columns.mobile_number') }}">
        <div v-if="errors.has('mobile_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mobile_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('landline_number'), 'has-success': fields.landline_number && fields.landline_number.valid }">
    <label for="landline_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.landline_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.landline_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('landline_number'), 'form-control-success': fields.landline_number && fields.landline_number.valid}" id="landline_number" name="landline_number" placeholder="{{ trans('admin.dealer-show-room.columns.landline_number') }}">
        <div v-if="errors.has('landline_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('landline_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('whatsapp_number'), 'has-success': fields.whatsapp_number && fields.whatsapp_number.valid }">
    <label for="whatsapp_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.whatsapp_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.whatsapp_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('whatsapp_number'), 'form-control-success': fields.whatsapp_number && fields.whatsapp_number.valid}" id="whatsapp_number" name="whatsapp_number" placeholder="{{ trans('admin.dealer-show-room.columns.whatsapp_number') }}">
        <div v-if="errors.has('whatsapp_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('whatsapp_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dealer_id'), 'has-success': fields.dealer_id && fields.dealer_id.valid }">
    <label for="dealer_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.dealer_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dealer_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dealer_id'), 'form-control-success': fields.dealer_id && fields.dealer_id.valid}" id="dealer_id" name="dealer_id" placeholder="{{ trans('admin.dealer-show-room.columns.dealer_id') }}">
        <div v-if="errors.has('dealer_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dealer_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('market_id'), 'has-success': fields.market_id && fields.market_id.valid }">
    <label for="market_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dealer-show-room.columns.market_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.market_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('market_id'), 'form-control-success': fields.market_id && fields.market_id.valid}" id="market_id" name="market_id" placeholder="{{ trans('admin.dealer-show-room.columns.market_id') }}">
        <div v-if="errors.has('market_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('market_id') }}</div>
    </div>
</div>


