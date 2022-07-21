<div class="form-group row align-items-center" :class="{'has-danger': errors.has('auto_ad_id'), 'has-success': fields.auto_ad_id && fields.auto_ad_id.valid }">
    <label for="auto_ad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad-option.columns.auto_ad_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.auto_ad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('auto_ad_id'), 'form-control-success': fields.auto_ad_id && fields.auto_ad_id.valid}" id="auto_ad_id" name="auto_ad_id" placeholder="{{ trans('admin.auto-ad-option.columns.auto_ad_id') }}">
        <div v-if="errors.has('auto_ad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('auto_ad_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('auto_option_id'), 'has-success': fields.auto_option_id && fields.auto_option_id.valid }">
    <label for="auto_option_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-ad-option.columns.auto_option_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.auto_option_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('auto_option_id'), 'form-control-success': fields.auto_option_id && fields.auto_option_id.valid}" id="auto_option_id" name="auto_option_id" placeholder="{{ trans('admin.auto-ad-option.columns.auto_option_id') }}">
        <div v-if="errors.has('auto_option_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('auto_option_id') }}</div>
    </div>
</div>


