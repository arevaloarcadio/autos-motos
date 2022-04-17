<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.brand.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.brand.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('logo'), 'has-success': fields.logo && fields.logo.valid }">
    <label for="logo" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.brand.columns.logo') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.logo" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('logo'), 'form-control-success': fields.logo && fields.logo.valid}" id="logo" name="logo" placeholder="{{ trans('admin.brand.columns.logo') }}">
        <div v-if="errors.has('logo')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('logo') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('top'), 'has-success': fields.top && fields.top.valid }">
    <label for="top" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.brand.columns.top') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.top" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('top'), 'form-control-success': fields.top && fields.top.valid}" id="top" name="top" placeholder="{{ trans('admin.brand.columns.top') }}">
        <div v-if="errors.has('top')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('top') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('slug'), 'has-success': fields.slug && fields.slug.valid }">
    <label for="slug" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.brand.columns.slug') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.slug" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('slug'), 'form-control-success': fields.slug && fields.slug.valid}" id="slug" name="slug" placeholder="{{ trans('admin.brand.columns.slug') }}">
        <div v-if="errors.has('slug')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('slug') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('meta_title'), 'has-success': fields.meta_title && fields.meta_title.valid }">
    <label for="meta_title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.brand.columns.meta_title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.meta_title" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('meta_title'), 'form-control-success': fields.meta_title && fields.meta_title.valid}" id="meta_title" name="meta_title" placeholder="{{ trans('admin.brand.columns.meta_title') }}">
        <div v-if="errors.has('meta_title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('meta_title') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('meta_description'), 'has-success': fields.meta_description && fields.meta_description.valid }">
    <label for="meta_description" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.brand.columns.meta_description') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <textarea class="form-control" v-model="form.meta_description" v-validate="''" id="meta_description" name="meta_description"></textarea>
        </div>
        <div v-if="errors.has('meta_description')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('meta_description') }}</div>
    </div>
</div>


