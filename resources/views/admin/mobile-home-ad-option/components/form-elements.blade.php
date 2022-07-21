<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mobile_home_ad_id'), 'has-success': fields.mobile_home_ad_id && fields.mobile_home_ad_id.valid }">
    <label for="mobile_home_ad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad-option.columns.mobile_home_ad_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mobile_home_ad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mobile_home_ad_id'), 'form-control-success': fields.mobile_home_ad_id && fields.mobile_home_ad_id.valid}" id="mobile_home_ad_id" name="mobile_home_ad_id" placeholder="{{ trans('admin.mobile-home-ad-option.columns.mobile_home_ad_id') }}">
        <div v-if="errors.has('mobile_home_ad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mobile_home_ad_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('option_id'), 'has-success': fields.option_id && fields.option_id.valid }">
    <label for="option_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.mobile-home-ad-option.columns.option_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.option_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option_id'), 'form-control-success': fields.option_id && fields.option_id.valid}" id="option_id" name="option_id" placeholder="{{ trans('admin.mobile-home-ad-option.columns.option_id') }}">
        <div v-if="errors.has('option_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('option_id') }}</div>
    </div>
</div>


