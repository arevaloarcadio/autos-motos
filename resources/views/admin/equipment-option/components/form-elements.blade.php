<div class="form-group row align-items-center" :class="{'has-danger': errors.has('equipment_id'), 'has-success': fields.equipment_id && fields.equipment_id.valid }">
    <label for="equipment_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.equipment-option.columns.equipment_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.equipment_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('equipment_id'), 'form-control-success': fields.equipment_id && fields.equipment_id.valid}" id="equipment_id" name="equipment_id" placeholder="{{ trans('admin.equipment-option.columns.equipment_id') }}">
        <div v-if="errors.has('equipment_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('equipment_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('option_id'), 'has-success': fields.option_id && fields.option_id.valid }">
    <label for="option_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.equipment-option.columns.option_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.option_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option_id'), 'form-control-success': fields.option_id && fields.option_id.valid}" id="option_id" name="option_id" placeholder="{{ trans('admin.equipment-option.columns.option_id') }}">
        <div v-if="errors.has('option_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('option_id') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('is_base'), 'has-success': fields.is_base && fields.is_base.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_base" type="checkbox" v-model="form.is_base" v-validate="''" data-vv-name="is_base"  name="is_base_fake_element">
        <label class="form-check-label" for="is_base">
            {{ trans('admin.equipment-option.columns.is_base') }}
        </label>
        <input type="hidden" name="is_base" :value="form.is_base">
        <div v-if="errors.has('is_base')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_base') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_type'), 'has-success': fields.ad_type && fields.ad_type.valid }">
    <label for="ad_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.equipment-option.columns.ad_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_type'), 'form-control-success': fields.ad_type && fields.ad_type.valid}" id="ad_type" name="ad_type" placeholder="{{ trans('admin.equipment-option.columns.ad_type') }}">
        <div v-if="errors.has('ad_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_type') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('external_id'), 'has-success': fields.external_id && fields.external_id.valid }">
    <label for="external_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.equipment-option.columns.external_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.external_id" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('external_id'), 'form-control-success': fields.external_id && fields.external_id.valid}" id="external_id" name="external_id" placeholder="{{ trans('admin.equipment-option.columns.external_id') }}">
        <div v-if="errors.has('external_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('external_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('external_updated_at'), 'has-success': fields.external_updated_at && fields.external_updated_at.valid }">
    <label for="external_updated_at" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.equipment-option.columns.external_updated_at') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div class="input-group input-group--custom">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <datetime v-model="form.external_updated_at" :config="datetimePickerConfig" v-validate="'date_format:yyyy-MM-dd HH:mm:ss'" class="flatpickr" :class="{'form-control-danger': errors.has('external_updated_at'), 'form-control-success': fields.external_updated_at && fields.external_updated_at.valid}" id="external_updated_at" name="external_updated_at" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_date_and_time') }}"></datetime>
        </div>
        <div v-if="errors.has('external_updated_at')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('external_updated_at') }}</div>
    </div>
</div>


