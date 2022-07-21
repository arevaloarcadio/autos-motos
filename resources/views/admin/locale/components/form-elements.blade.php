<div class="form-group row align-items-center" :class="{'has-danger': errors.has('internal_name'), 'has-success': fields.internal_name && fields.internal_name.valid }">
    <label for="internal_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.locale.columns.internal_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.internal_name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('internal_name'), 'form-control-success': fields.internal_name && fields.internal_name.valid}" id="internal_name" name="internal_name" placeholder="{{ trans('admin.locale.columns.internal_name') }}">
        <div v-if="errors.has('internal_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('internal_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('code'), 'has-success': fields.code && fields.code.valid }">
    <label for="code" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.locale.columns.code') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.code" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('code'), 'form-control-success': fields.code && fields.code.valid}" id="code" name="code" placeholder="{{ trans('admin.locale.columns.code') }}">
        <div v-if="errors.has('code')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('code') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('icon'), 'has-success': fields.icon && fields.icon.valid }">
    <label for="icon" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.locale.columns.icon') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.icon" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('icon'), 'form-control-success': fields.icon && fields.icon.valid}" id="icon" name="icon" placeholder="{{ trans('admin.locale.columns.icon') }}">
        <div v-if="errors.has('icon')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('icon') }}</div>
    </div>
</div>


