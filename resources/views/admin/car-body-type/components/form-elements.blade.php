<div class="form-group row align-items-center" :class="{'has-danger': errors.has('internal_name'), 'has-success': fields.internal_name && fields.internal_name.valid }">
    <label for="internal_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-body-type.columns.internal_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.internal_name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('internal_name'), 'form-control-success': fields.internal_name && fields.internal_name.valid}" id="internal_name" name="internal_name" placeholder="{{ trans('admin.car-body-type.columns.internal_name') }}">
        <div v-if="errors.has('internal_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('internal_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('slug'), 'has-success': fields.slug && fields.slug.valid }">
    <label for="slug" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-body-type.columns.slug') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.slug" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('slug'), 'form-control-success': fields.slug && fields.slug.valid}" id="slug" name="slug" placeholder="{{ trans('admin.car-body-type.columns.slug') }}">
        <div v-if="errors.has('slug')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('slug') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('icon_url'), 'has-success': fields.icon_url && fields.icon_url.valid }">
    <label for="icon_url" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-body-type.columns.icon_url') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.icon_url" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('icon_url'), 'form-control-success': fields.icon_url && fields.icon_url.valid}" id="icon_url" name="icon_url" placeholder="{{ trans('admin.car-body-type.columns.icon_url') }}">
        <div v-if="errors.has('icon_url')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('icon_url') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('external_name'), 'has-success': fields.external_name && fields.external_name.valid }">
    <label for="external_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-body-type.columns.external_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.external_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('external_name'), 'form-control-success': fields.external_name && fields.external_name.valid}" id="external_name" name="external_name" placeholder="{{ trans('admin.car-body-type.columns.external_name') }}">
        <div v-if="errors.has('external_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('external_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_type'), 'has-success': fields.ad_type && fields.ad_type.valid }">
    <label for="ad_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-body-type.columns.ad_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_type'), 'form-control-success': fields.ad_type && fields.ad_type.valid}" id="ad_type" name="ad_type" placeholder="{{ trans('admin.car-body-type.columns.ad_type') }}">
        <div v-if="errors.has('ad_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_type') }}</div>
    </div>
</div>


