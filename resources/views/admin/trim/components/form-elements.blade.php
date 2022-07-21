<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.trim.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('model_id'), 'has-success': fields.model_id && fields.model_id.valid }">
    <label for="model_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim.columns.model_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.model_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('model_id'), 'form-control-success': fields.model_id && fields.model_id.valid}" id="model_id" name="model_id" placeholder="{{ trans('admin.trim.columns.model_id') }}">
        <div v-if="errors.has('model_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('model_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('series_id'), 'has-success': fields.series_id && fields.series_id.valid }">
    <label for="series_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim.columns.series_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.series_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('series_id'), 'form-control-success': fields.series_id && fields.series_id.valid}" id="series_id" name="series_id" placeholder="{{ trans('admin.trim.columns.series_id') }}">
        <div v-if="errors.has('series_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('series_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('production_year_start'), 'has-success': fields.production_year_start && fields.production_year_start.valid }">
    <label for="production_year_start" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim.columns.production_year_start') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.production_year_start" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('production_year_start'), 'form-control-success': fields.production_year_start && fields.production_year_start.valid}" id="production_year_start" name="production_year_start" placeholder="{{ trans('admin.trim.columns.production_year_start') }}">
        <div v-if="errors.has('production_year_start')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('production_year_start') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('production_year_end'), 'has-success': fields.production_year_end && fields.production_year_end.valid }">
    <label for="production_year_end" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim.columns.production_year_end') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.production_year_end" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('production_year_end'), 'form-control-success': fields.production_year_end && fields.production_year_end.valid}" id="production_year_end" name="production_year_end" placeholder="{{ trans('admin.trim.columns.production_year_end') }}">
        <div v-if="errors.has('production_year_end')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('production_year_end') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('is_active'), 'has-success': fields.is_active && fields.is_active.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_active" type="checkbox" v-model="form.is_active" v-validate="''" data-vv-name="is_active"  name="is_active_fake_element">
        <label class="form-check-label" for="is_active">
            {{ trans('admin.trim.columns.is_active') }}
        </label>
        <input type="hidden" name="is_active" :value="form.is_active">
        <div v-if="errors.has('is_active')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_active') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_type'), 'has-success': fields.ad_type && fields.ad_type.valid }">
    <label for="ad_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim.columns.ad_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_type'), 'form-control-success': fields.ad_type && fields.ad_type.valid}" id="ad_type" name="ad_type" placeholder="{{ trans('admin.trim.columns.ad_type') }}">
        <div v-if="errors.has('ad_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_type') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('external_id'), 'has-success': fields.external_id && fields.external_id.valid }">
    <label for="external_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim.columns.external_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.external_id" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('external_id'), 'form-control-success': fields.external_id && fields.external_id.valid}" id="external_id" name="external_id" placeholder="{{ trans('admin.trim.columns.external_id') }}">
        <div v-if="errors.has('external_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('external_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('external_updated_at'), 'has-success': fields.external_updated_at && fields.external_updated_at.valid }">
    <label for="external_updated_at" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.trim.columns.external_updated_at') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div class="input-group input-group--custom">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <datetime v-model="form.external_updated_at" :config="datetimePickerConfig" v-validate="'date_format:yyyy-MM-dd HH:mm:ss'" class="flatpickr" :class="{'form-control-danger': errors.has('external_updated_at'), 'form-control-success': fields.external_updated_at && fields.external_updated_at.valid}" id="external_updated_at" name="external_updated_at" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_date_and_time') }}"></datetime>
        </div>
        <div v-if="errors.has('external_updated_at')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('external_updated_at') }}</div>
    </div>
</div>


