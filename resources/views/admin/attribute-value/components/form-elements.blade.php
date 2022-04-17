<div class="form-group row align-items-center" :class="{'has-danger': errors.has('attribute_id'), 'has-success': fields.attribute_id && fields.attribute_id.valid }">
    <label for="attribute_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.attribute-value.columns.attribute_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.attribute_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('attribute_id'), 'form-control-success': fields.attribute_id && fields.attribute_id.valid}" id="attribute_id" name="attribute_id" placeholder="{{ trans('admin.attribute-value.columns.attribute_id') }}">
        <div v-if="errors.has('attribute_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('attribute_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('value'), 'has-success': fields.value && fields.value.valid }">
    <label for="value" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.attribute-value.columns.value') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.value" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('value'), 'form-control-success': fields.value && fields.value.valid}" id="value" name="value" placeholder="{{ trans('admin.attribute-value.columns.value') }}">
        <div v-if="errors.has('value')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('value') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('color_code'), 'has-success': fields.color_code && fields.color_code.valid }">
    <label for="color_code" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.attribute-value.columns.color_code') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.color_code" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('color_code'), 'form-control-success': fields.color_code && fields.color_code.valid}" id="color_code" name="color_code" placeholder="{{ trans('admin.attribute-value.columns.color_code') }}">
        <div v-if="errors.has('color_code')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('color_code') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('ads_type'), 'has-success': fields.ads_type && fields.ads_type.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="ads_type" type="checkbox" v-model="form.ads_type" v-validate="''" data-vv-name="ads_type"  name="ads_type_fake_element">
        <label class="form-check-label" for="ads_type">
            {{ trans('admin.attribute-value.columns.ads_type') }}
        </label>
        <input type="hidden" name="ads_type" :value="form.ads_type">
        <div v-if="errors.has('ads_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ads_type') }}</div>
    </div>
</div>


