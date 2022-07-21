<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_id'), 'has-success': fields.ad_id && fields.ad_id.valid }">
    <label for="ad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.ad_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_id'), 'form-control-success': fields.ad_id && fields.ad_id.valid}" id="ad_id" name="ad_id" placeholder="{{ trans('admin.auto-ad.columns.ad_id') }}">
        <div v-if="errors.has('ad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('model_id'), 'has-success': fields.model_id && fields.model_id.valid }">
    <label for="model_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.model_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.model_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('model_id'), 'form-control-success': fields.model_id && fields.model_id.valid}" id="model_id" name="model_id" placeholder="{{ trans('admin.auto-ad.columns.model_id') }}">
        <div v-if="errors.has('model_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('model_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_registration_month'), 'has-success': fields.first_registration_month && fields.first_registration_month.valid }">
    <label for="first_registration_month" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.first_registration_month') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.first_registration_month" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_registration_month'), 'form-control-success': fields.first_registration_month && fields.first_registration_month.valid}" id="first_registration_month" name="first_registration_month" placeholder="{{ trans('admin.auto-ad.columns.first_registration_month') }}">
        <div v-if="errors.has('first_registration_month')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_registration_month') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_registration_year'), 'has-success': fields.first_registration_year && fields.first_registration_year.valid }">
    <label for="first_registration_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.first_registration_year') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.first_registration_year" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_registration_year'), 'form-control-success': fields.first_registration_year && fields.first_registration_year.valid}" id="first_registration_year" name="first_registration_year" placeholder="{{ trans('admin.auto-ad.columns.first_registration_year') }}">
        <div v-if="errors.has('first_registration_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_registration_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('engine_displacement'), 'has-success': fields.engine_displacement && fields.engine_displacement.valid }">
    <label for="engine_displacement" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.engine_displacement') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.engine_displacement" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('engine_displacement'), 'form-control-success': fields.engine_displacement && fields.engine_displacement.valid}" id="engine_displacement" name="engine_displacement" placeholder="{{ trans('admin.auto-ad.columns.engine_displacement') }}">
        <div v-if="errors.has('engine_displacement')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('engine_displacement') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('power_hp'), 'has-success': fields.power_hp && fields.power_hp.valid }">
    <label for="power_hp" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.power_hp') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.power_hp" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('power_hp'), 'form-control-success': fields.power_hp && fields.power_hp.valid}" id="power_hp" name="power_hp" placeholder="{{ trans('admin.auto-ad.columns.power_hp') }}">
        <div v-if="errors.has('power_hp')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('power_hp') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('owners'), 'has-success': fields.owners && fields.owners.valid }">
    <label for="owners" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.owners') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.owners" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('owners'), 'form-control-success': fields.owners && fields.owners.valid}" id="owners" name="owners" placeholder="{{ trans('admin.auto-ad.columns.owners') }}">
        <div v-if="errors.has('owners')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('owners') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('inspection_valid_until_month'), 'has-success': fields.inspection_valid_until_month && fields.inspection_valid_until_month.valid }">
    <label for="inspection_valid_until_month" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.inspection_valid_until_month') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.inspection_valid_until_month" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('inspection_valid_until_month'), 'form-control-success': fields.inspection_valid_until_month && fields.inspection_valid_until_month.valid}" id="inspection_valid_until_month" name="inspection_valid_until_month" placeholder="{{ trans('admin.auto-ad.columns.inspection_valid_until_month') }}">
        <div v-if="errors.has('inspection_valid_until_month')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('inspection_valid_until_month') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('inspection_valid_until_year'), 'has-success': fields.inspection_valid_until_year && fields.inspection_valid_until_year.valid }">
    <label for="inspection_valid_until_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.inspection_valid_until_year') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.inspection_valid_until_year" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('inspection_valid_until_year'), 'form-control-success': fields.inspection_valid_until_year && fields.inspection_valid_until_year.valid}" id="inspection_valid_until_year" name="inspection_valid_until_year" placeholder="{{ trans('admin.auto-ad.columns.inspection_valid_until_year') }}">
        <div v-if="errors.has('inspection_valid_until_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('inspection_valid_until_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('make_id'), 'has-success': fields.make_id && fields.make_id.valid }">
    <label for="make_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.make_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.make_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('make_id'), 'form-control-success': fields.make_id && fields.make_id.valid}" id="make_id" name="make_id" placeholder="{{ trans('admin.auto-ad.columns.make_id') }}">
        <div v-if="errors.has('make_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('make_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('generation_id'), 'has-success': fields.generation_id && fields.generation_id.valid }">
    <label for="generation_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.generation_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.generation_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('generation_id'), 'form-control-success': fields.generation_id && fields.generation_id.valid}" id="generation_id" name="generation_id" placeholder="{{ trans('admin.auto-ad.columns.generation_id') }}">
        <div v-if="errors.has('generation_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('generation_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_transmission_type_id'), 'has-success': fields.ad_transmission_type_id && fields.ad_transmission_type_id.valid }">
    <label for="ad_transmission_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.ad_transmission_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_transmission_type_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_transmission_type_id'), 'form-control-success': fields.ad_transmission_type_id && fields.ad_transmission_type_id.valid}" id="ad_transmission_type_id" name="ad_transmission_type_id" placeholder="{{ trans('admin.auto-ad.columns.ad_transmission_type_id') }}">
        <div v-if="errors.has('ad_transmission_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_transmission_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('series_id'), 'has-success': fields.series_id && fields.series_id.valid }">
    <label for="series_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.series_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.series_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('series_id'), 'form-control-success': fields.series_id && fields.series_id.valid}" id="series_id" name="series_id" placeholder="{{ trans('admin.auto-ad.columns.series_id') }}">
        <div v-if="errors.has('series_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('series_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('trim_id'), 'has-success': fields.trim_id && fields.trim_id.valid }">
    <label for="trim_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.trim_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.trim_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('trim_id'), 'form-control-success': fields.trim_id && fields.trim_id.valid}" id="trim_id" name="trim_id" placeholder="{{ trans('admin.auto-ad.columns.trim_id') }}">
        <div v-if="errors.has('trim_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('trim_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('equipment_id'), 'has-success': fields.equipment_id && fields.equipment_id.valid }">
    <label for="equipment_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.equipment_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.equipment_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('equipment_id'), 'form-control-success': fields.equipment_id && fields.equipment_id.valid}" id="equipment_id" name="equipment_id" placeholder="{{ trans('admin.auto-ad.columns.equipment_id') }}">
        <div v-if="errors.has('equipment_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('equipment_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('additional_vehicle_info'), 'has-success': fields.additional_vehicle_info && fields.additional_vehicle_info.valid }">
    <label for="additional_vehicle_info" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.additional_vehicle_info') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.additional_vehicle_info" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('additional_vehicle_info'), 'form-control-success': fields.additional_vehicle_info && fields.additional_vehicle_info.valid}" id="additional_vehicle_info" name="additional_vehicle_info" placeholder="{{ trans('admin.auto-ad.columns.additional_vehicle_info') }}">
        <div v-if="errors.has('additional_vehicle_info')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('additional_vehicle_info') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('seats'), 'has-success': fields.seats && fields.seats.valid }">
    <label for="seats" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.seats') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.seats" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('seats'), 'form-control-success': fields.seats && fields.seats.valid}" id="seats" name="seats" placeholder="{{ trans('admin.auto-ad.columns.seats') }}">
        <div v-if="errors.has('seats')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('seats') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('fuel_consumption'), 'has-success': fields.fuel_consumption && fields.fuel_consumption.valid }">
    <label for="fuel_consumption" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.fuel_consumption') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.fuel_consumption" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('fuel_consumption'), 'form-control-success': fields.fuel_consumption && fields.fuel_consumption.valid}" id="fuel_consumption" name="fuel_consumption" placeholder="{{ trans('admin.auto-ad.columns.fuel_consumption') }}">
        <div v-if="errors.has('fuel_consumption')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('fuel_consumption') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('co2_emissions'), 'has-success': fields.co2_emissions && fields.co2_emissions.valid }">
    <label for="co2_emissions" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.co2_emissions') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.co2_emissions" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('co2_emissions'), 'form-control-success': fields.co2_emissions && fields.co2_emissions.valid}" id="co2_emissions" name="co2_emissions" placeholder="{{ trans('admin.auto-ad.columns.co2_emissions') }}">
        <div v-if="errors.has('co2_emissions')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('co2_emissions') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('latitude'), 'has-success': fields.latitude && fields.latitude.valid }">
    <label for="latitude" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.latitude') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.latitude" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('latitude'), 'form-control-success': fields.latitude && fields.latitude.valid}" id="latitude" name="latitude" placeholder="{{ trans('admin.auto-ad.columns.latitude') }}">
        <div v-if="errors.has('latitude')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('latitude') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('longitude'), 'has-success': fields.longitude && fields.longitude.valid }">
    <label for="longitude" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.longitude') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.longitude" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('longitude'), 'form-control-success': fields.longitude && fields.longitude.valid}" id="longitude" name="longitude" placeholder="{{ trans('admin.auto-ad.columns.longitude') }}">
        <div v-if="errors.has('longitude')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('longitude') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_drive_type_id'), 'has-success': fields.ad_drive_type_id && fields.ad_drive_type_id.valid }">
    <label for="ad_drive_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.ad_drive_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_drive_type_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_drive_type_id'), 'form-control-success': fields.ad_drive_type_id && fields.ad_drive_type_id.valid}" id="ad_drive_type_id" name="ad_drive_type_id" placeholder="{{ trans('admin.auto-ad.columns.ad_drive_type_id') }}">
        <div v-if="errors.has('ad_drive_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_drive_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_body_type_id'), 'has-success': fields.ad_body_type_id && fields.ad_body_type_id.valid }">
    <label for="ad_body_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.ad_body_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_body_type_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_body_type_id'), 'form-control-success': fields.ad_body_type_id && fields.ad_body_type_id.valid}" id="ad_body_type_id" name="ad_body_type_id" placeholder="{{ trans('admin.auto-ad.columns.ad_body_type_id') }}">
        <div v-if="errors.has('ad_body_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_body_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('price'), 'has-success': fields.price && fields.price.valid }">
    <label for="price" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.price') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.price" v-validate="'required|decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('price'), 'form-control-success': fields.price && fields.price.valid}" id="price" name="price" placeholder="{{ trans('admin.auto-ad.columns.price') }}">
        <div v-if="errors.has('price')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('price') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('first_name'), 'has-success': fields.first_name && fields.first_name.valid }">
    <label for="first_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.first_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.first_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('first_name'), 'form-control-success': fields.first_name && fields.first_name.valid}" id="first_name" name="first_name" placeholder="{{ trans('admin.auto-ad.columns.first_name') }}">
        <div v-if="errors.has('first_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_name') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('price_contains_vat'), 'has-success': fields.price_contains_vat && fields.price_contains_vat.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="price_contains_vat" type="checkbox" v-model="form.price_contains_vat" v-validate="''" data-vv-name="price_contains_vat"  name="price_contains_vat_fake_element">
        <label class="form-check-label" for="price_contains_vat">
            {{ trans('admin.auto-ad.columns.price_contains_vat') }}
        </label>
        <input type="hidden" name="price_contains_vat" :value="form.price_contains_vat">
        <div v-if="errors.has('price_contains_vat')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('price_contains_vat') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('vin'), 'has-success': fields.vin && fields.vin.valid }">
    <label for="vin" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.vin') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.vin" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('vin'), 'form-control-success': fields.vin && fields.vin.valid}" id="vin" name="vin" placeholder="{{ trans('admin.auto-ad.columns.vin') }}">
        <div v-if="errors.has('vin')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('vin') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('doors'), 'has-success': fields.doors && fields.doors.valid }">
    <label for="doors" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.doors') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.doors" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('doors'), 'form-control-success': fields.doors && fields.doors.valid}" id="doors" name="doors" placeholder="{{ trans('admin.auto-ad.columns.doors') }}">
        <div v-if="errors.has('doors')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('doors') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mileage'), 'has-success': fields.mileage && fields.mileage.valid }">
    <label for="mileage" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.mileage') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mileage" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mileage'), 'form-control-success': fields.mileage && fields.mileage.valid}" id="mileage" name="mileage" placeholder="{{ trans('admin.auto-ad.columns.mileage') }}">
        <div v-if="errors.has('mileage')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mileage') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('exterior_color'), 'has-success': fields.exterior_color && fields.exterior_color.valid }">
    <label for="exterior_color" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.exterior_color') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.exterior_color" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('exterior_color'), 'form-control-success': fields.exterior_color && fields.exterior_color.valid}" id="exterior_color" name="exterior_color" placeholder="{{ trans('admin.auto-ad.columns.exterior_color') }}">
        <div v-if="errors.has('exterior_color')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('exterior_color') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('interior_color'), 'has-success': fields.interior_color && fields.interior_color.valid }">
    <label for="interior_color" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.interior_color') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.interior_color" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('interior_color'), 'form-control-success': fields.interior_color && fields.interior_color.valid}" id="interior_color" name="interior_color" placeholder="{{ trans('admin.auto-ad.columns.interior_color') }}">
        <div v-if="errors.has('interior_color')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('interior_color') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('condition'), 'has-success': fields.condition && fields.condition.valid }">
    <label for="condition" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.condition') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.condition" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('condition'), 'form-control-success': fields.condition && fields.condition.valid}" id="condition" name="condition" placeholder="{{ trans('admin.auto-ad.columns.condition') }}">
        <div v-if="errors.has('condition')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('condition') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dealer_id'), 'has-success': fields.dealer_id && fields.dealer_id.valid }">
    <label for="dealer_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.dealer_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dealer_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dealer_id'), 'form-control-success': fields.dealer_id && fields.dealer_id.valid}" id="dealer_id" name="dealer_id" placeholder="{{ trans('admin.auto-ad.columns.dealer_id') }}">
        <div v-if="errors.has('dealer_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dealer_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dealer_show_room_id'), 'has-success': fields.dealer_show_room_id && fields.dealer_show_room_id.valid }">
    <label for="dealer_show_room_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.dealer_show_room_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dealer_show_room_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dealer_show_room_id'), 'form-control-success': fields.dealer_show_room_id && fields.dealer_show_room_id.valid}" id="dealer_show_room_id" name="dealer_show_room_id" placeholder="{{ trans('admin.auto-ad.columns.dealer_show_room_id') }}">
        <div v-if="errors.has('dealer_show_room_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dealer_show_room_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('last_name'), 'has-success': fields.last_name && fields.last_name.valid }">
    <label for="last_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.last_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.last_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('last_name'), 'form-control-success': fields.last_name && fields.last_name.valid}" id="last_name" name="last_name" placeholder="{{ trans('admin.auto-ad.columns.last_name') }}">
        <div v-if="errors.has('last_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('last_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_fuel_type_id'), 'has-success': fields.ad_fuel_type_id && fields.ad_fuel_type_id.valid }">
    <label for="ad_fuel_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.ad_fuel_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_fuel_type_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_fuel_type_id'), 'form-control-success': fields.ad_fuel_type_id && fields.ad_fuel_type_id.valid}" id="ad_fuel_type_id" name="ad_fuel_type_id" placeholder="{{ trans('admin.auto-ad.columns.ad_fuel_type_id') }}">
        <div v-if="errors.has('ad_fuel_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_fuel_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('email_address'), 'has-success': fields.email_address && fields.email_address.valid }">
    <label for="email_address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.email_address') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.email_address" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('email_address'), 'form-control-success': fields.email_address && fields.email_address.valid}" id="email_address" name="email_address" placeholder="{{ trans('admin.auto-ad.columns.email_address') }}">
        <div v-if="errors.has('email_address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email_address') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('address'), 'has-success': fields.address && fields.address.valid }">
    <label for="address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.address') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <textarea class="form-control" v-model="form.address" v-validate="'required'" id="address" name="address"></textarea>
        </div>
        <div v-if="errors.has('address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('address') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('zip_code'), 'has-success': fields.zip_code && fields.zip_code.valid }">
    <label for="zip_code" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.zip_code') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.zip_code" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('zip_code'), 'form-control-success': fields.zip_code && fields.zip_code.valid}" id="zip_code" name="zip_code" placeholder="{{ trans('admin.auto-ad.columns.zip_code') }}">
        <div v-if="errors.has('zip_code')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('zip_code') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('city'), 'has-success': fields.city && fields.city.valid }">
    <label for="city" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.city') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.city" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('city'), 'form-control-success': fields.city && fields.city.valid}" id="city" name="city" placeholder="{{ trans('admin.auto-ad.columns.city') }}">
        <div v-if="errors.has('city')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('city') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('country'), 'has-success': fields.country && fields.country.valid }">
    <label for="country" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.country') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.country" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('country'), 'form-control-success': fields.country && fields.country.valid}" id="country" name="country" placeholder="{{ trans('admin.auto-ad.columns.country') }}">
        <div v-if="errors.has('country')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mobile_number'), 'has-success': fields.mobile_number && fields.mobile_number.valid }">
    <label for="mobile_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.mobile_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mobile_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mobile_number'), 'form-control-success': fields.mobile_number && fields.mobile_number.valid}" id="mobile_number" name="mobile_number" placeholder="{{ trans('admin.auto-ad.columns.mobile_number') }}">
        <div v-if="errors.has('mobile_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mobile_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('landline_number'), 'has-success': fields.landline_number && fields.landline_number.valid }">
    <label for="landline_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.landline_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.landline_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('landline_number'), 'form-control-success': fields.landline_number && fields.landline_number.valid}" id="landline_number" name="landline_number" placeholder="{{ trans('admin.auto-ad.columns.landline_number') }}">
        <div v-if="errors.has('landline_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('landline_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('whatsapp_number'), 'has-success': fields.whatsapp_number && fields.whatsapp_number.valid }">
    <label for="whatsapp_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.whatsapp_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.whatsapp_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('whatsapp_number'), 'form-control-success': fields.whatsapp_number && fields.whatsapp_number.valid}" id="whatsapp_number" name="whatsapp_number" placeholder="{{ trans('admin.auto-ad.columns.whatsapp_number') }}">
        <div v-if="errors.has('whatsapp_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('whatsapp_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('youtube_link'), 'has-success': fields.youtube_link && fields.youtube_link.valid }">
    <label for="youtube_link" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.youtube_link') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.youtube_link" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('youtube_link'), 'form-control-success': fields.youtube_link && fields.youtube_link.valid}" id="youtube_link" name="youtube_link" placeholder="{{ trans('admin.auto-ad.columns.youtube_link') }}">
        <div v-if="errors.has('youtube_link')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('youtube_link') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('geocoding_status'), 'has-success': fields.geocoding_status && fields.geocoding_status.valid }">
    <label for="geocoding_status" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad.columns.geocoding_status') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.geocoding_status" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('geocoding_status'), 'form-control-success': fields.geocoding_status && fields.geocoding_status.valid}" id="geocoding_status" name="geocoding_status" placeholder="{{ trans('admin.auto-ad.columns.geocoding_status') }}">
        <div v-if="errors.has('geocoding_status')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('geocoding_status') }}</div>
    </div>
</div>


