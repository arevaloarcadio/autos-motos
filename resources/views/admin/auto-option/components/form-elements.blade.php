<div class="form-group row align-items-center" :class="{'has-danger': errors.has('internal_name'), 'has-success': fields.internal_name && fields.internal_name.valid }">
    <label for="internal_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-option.columns.internal_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.internal_name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('internal_name'), 'form-control-success': fields.internal_name && fields.internal_name.valid}" id="internal_name" name="internal_name" placeholder="{{ trans('admin.auto-option.columns.internal_name') }}">
        <div v-if="errors.has('internal_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('internal_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('slug'), 'has-success': fields.slug && fields.slug.valid }">
    <label for="slug" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-option.columns.slug') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.slug" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('slug'), 'form-control-success': fields.slug && fields.slug.valid}" id="slug" name="slug" placeholder="{{ trans('admin.auto-option.columns.slug') }}">
        <div v-if="errors.has('slug')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('slug') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('parent_id'), 'has-success': fields.parent_id && fields.parent_id.valid }">
    <label for="parent_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-option.columns.parent_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.parent_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('parent_id'), 'form-control-success': fields.parent_id && fields.parent_id.valid}" id="parent_id" name="parent_id" placeholder="{{ trans('admin.auto-option.columns.parent_id') }}">
        <div v-if="errors.has('parent_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_type'), 'has-success': fields.ad_type && fields.ad_type.valid }">
    <label for="ad_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.auto-option.columns.ad_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_type'), 'form-control-success': fields.ad_type && fields.ad_type.valid}" id="ad_type" name="ad_type" placeholder="{{ trans('admin.auto-option.columns.ad_type') }}">
        <div v-if="errors.has('ad_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_type') }}</div>
    </div>
</div>


