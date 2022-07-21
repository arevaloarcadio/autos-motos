<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_image_id'), 'has-success': fields.ad_image_id && fields.ad_image_id.valid }">
    <label for="ad_image_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.ad-image-version.columns.ad_image_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_image_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_image_id'), 'form-control-success': fields.ad_image_id && fields.ad_image_id.valid}" id="ad_image_id" name="ad_image_id" placeholder="{{ trans('admin.ad-image-version.columns.ad_image_id') }}">
        <div v-if="errors.has('ad_image_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_image_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.ad-image-version.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.ad-image-version.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('path'), 'has-success': fields.path && fields.path.valid }">
    <label for="path" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.ad-image-version.columns.path') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.path" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('path'), 'form-control-success': fields.path && fields.path.valid}" id="path" name="path" placeholder="{{ trans('admin.ad-image-version.columns.path') }}">
        <div v-if="errors.has('path')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('path') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('is_external'), 'has-success': fields.is_external && fields.is_external.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_external" type="checkbox" v-model="form.is_external" v-validate="''" data-vv-name="is_external"  name="is_external_fake_element">
        <label class="form-check-label" for="is_external">
            {{ trans('admin.ad-image-version.columns.is_external') }}
        </label>
        <input type="hidden" name="is_external" :value="form.is_external">
        <div v-if="errors.has('is_external')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_external') }}</div>
    </div>
</div>


