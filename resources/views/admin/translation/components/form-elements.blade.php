<div class="form-group row align-items-center" :class="{'has-danger': errors.has('locale_id'), 'has-success': fields.locale_id && fields.locale_id.valid }">
    <label for="locale_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.translation.columns.locale_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.locale_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('locale_id'), 'form-control-success': fields.locale_id && fields.locale_id.valid}" id="locale_id" name="locale_id" placeholder="{{ trans('admin.translation.columns.locale_id') }}">
        <div v-if="errors.has('locale_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('locale_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('translation_key'), 'has-success': fields.translation_key && fields.translation_key.valid }">
    <label for="translation_key" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.translation.columns.translation_key') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.translation_key" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('translation_key'), 'form-control-success': fields.translation_key && fields.translation_key.valid}" id="translation_key" name="translation_key" placeholder="{{ trans('admin.translation.columns.translation_key') }}">
        <div v-if="errors.has('translation_key')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('translation_key') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('translation_value'), 'has-success': fields.translation_value && fields.translation_value.valid }">
    <label for="translation_value" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.translation.columns.translation_value') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <textarea class="form-control" v-model="form.translation_value" v-validate="'required'" id="translation_value" name="translation_value"></textarea>
        </div>
        <div v-if="errors.has('translation_value')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('translation_value') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('resource_id'), 'has-success': fields.resource_id && fields.resource_id.valid }">
    <label for="resource_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.translation.columns.resource_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.resource_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('resource_id'), 'form-control-success': fields.resource_id && fields.resource_id.valid}" id="resource_id" name="resource_id" placeholder="{{ trans('admin.translation.columns.resource_id') }}">
        <div v-if="errors.has('resource_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('resource_id') }}</div>
    </div>
</div>


