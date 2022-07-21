<div class="form-group row align-items-center" :class="{'has-danger': errors.has('trim_id'), 'has-success': fields.trim_id && fields.trim_id.valid }">
    <label for="trim_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim-specification.columns.trim_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.trim_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('trim_id'), 'form-control-success': fields.trim_id && fields.trim_id.valid}" id="trim_id" name="trim_id" placeholder="{{ trans('admin.trim-specification.columns.trim_id') }}">
        <div v-if="errors.has('trim_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('trim_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('specification_id'), 'has-success': fields.specification_id && fields.specification_id.valid }">
    <label for="specification_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim-specification.columns.specification_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.specification_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('specification_id'), 'form-control-success': fields.specification_id && fields.specification_id.valid}" id="specification_id" name="specification_id" placeholder="{{ trans('admin.trim-specification.columns.specification_id') }}">
        <div v-if="errors.has('specification_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('specification_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('value'), 'has-success': fields.value && fields.value.valid }">
    <label for="value" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim-specification.columns.value') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.value" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('value'), 'form-control-success': fields.value && fields.value.valid}" id="value" name="value" placeholder="{{ trans('admin.trim-specification.columns.value') }}">
        <div v-if="errors.has('value')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('value') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('unit'), 'has-success': fields.unit && fields.unit.valid }">
    <label for="unit" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim-specification.columns.unit') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.unit" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('unit'), 'form-control-success': fields.unit && fields.unit.valid}" id="unit" name="unit" placeholder="{{ trans('admin.trim-specification.columns.unit') }}">
        <div v-if="errors.has('unit')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('unit') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_type'), 'has-success': fields.ad_type && fields.ad_type.valid }">
    <label for="ad_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim-specification.columns.ad_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_type'), 'form-control-success': fields.ad_type && fields.ad_type.valid}" id="ad_type" name="ad_type" placeholder="{{ trans('admin.trim-specification.columns.ad_type') }}">
        <div v-if="errors.has('ad_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_type') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('external_id'), 'has-success': fields.external_id && fields.external_id.valid }">
    <label for="external_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim-specification.columns.external_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.external_id" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('external_id'), 'form-control-success': fields.external_id && fields.external_id.valid}" id="external_id" name="external_id" placeholder="{{ trans('admin.trim-specification.columns.external_id') }}">
        <div v-if="errors.has('external_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('external_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('external_updated_at'), 'has-success': fields.external_updated_at && fields.external_updated_at.valid }">
    <label for="external_updated_at" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim-specification.columns.external_updated_at') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div class="input-group input-group--custom">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <datetime v-model="form.external_updated_at" :config="datetimePickerConfig" v-validate="'date_format:yyyy-MM-dd HH:mm:ss'" class="flatpickr" :class="{'form-control-danger': errors.has('external_updated_at'), 'form-control-success': fields.external_updated_at && fields.external_updated_at.valid}" id="external_updated_at" name="external_updated_at" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_date_and_time') }}"></datetime>
        </div>
        <div v-if="errors.has('external_updated_at')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('external_updated_at') }}</div>
    </div>
</div>


