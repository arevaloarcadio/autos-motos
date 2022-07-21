<div class="form-group row align-items-center" :class="{'has-danger': errors.has('internal_name'), 'has-success': fields.internal_name && fields.internal_name.valid }">
    <label for="internal_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.market.columns.internal_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.internal_name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('internal_name'), 'form-control-success': fields.internal_name && fields.internal_name.valid}" id="internal_name" name="internal_name" placeholder="{{ trans('admin.market.columns.internal_name') }}">
        <div v-if="errors.has('internal_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('internal_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('slug'), 'has-success': fields.slug && fields.slug.valid }">
    <label for="slug" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.market.columns.slug') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.slug" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('slug'), 'form-control-success': fields.slug && fields.slug.valid}" id="slug" name="slug" placeholder="{{ trans('admin.market.columns.slug') }}">
        <div v-if="errors.has('slug')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('slug') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('domain'), 'has-success': fields.domain && fields.domain.valid }">
    <label for="domain" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.market.columns.domain') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.domain" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('domain'), 'form-control-success': fields.domain && fields.domain.valid}" id="domain" name="domain" placeholder="{{ trans('admin.market.columns.domain') }}">
        <div v-if="errors.has('domain')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('domain') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('default_locale_id'), 'has-success': fields.default_locale_id && fields.default_locale_id.valid }">
    <label for="default_locale_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.market.columns.default_locale_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.default_locale_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('default_locale_id'), 'form-control-success': fields.default_locale_id && fields.default_locale_id.valid}" id="default_locale_id" name="default_locale_id" placeholder="{{ trans('admin.market.columns.default_locale_id') }}">
        <div v-if="errors.has('default_locale_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('default_locale_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('icon'), 'has-success': fields.icon && fields.icon.valid }">
    <label for="icon" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.market.columns.icon') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.icon" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('icon'), 'form-control-success': fields.icon && fields.icon.valid}" id="icon" name="icon" placeholder="{{ trans('admin.market.columns.icon') }}">
        <div v-if="errors.has('icon')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('icon') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mobile_number'), 'has-success': fields.mobile_number && fields.mobile_number.valid }">
    <label for="mobile_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.market.columns.mobile_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mobile_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mobile_number'), 'form-control-success': fields.mobile_number && fields.mobile_number.valid}" id="mobile_number" name="mobile_number" placeholder="{{ trans('admin.market.columns.mobile_number') }}">
        <div v-if="errors.has('mobile_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mobile_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('whatsapp_number'), 'has-success': fields.whatsapp_number && fields.whatsapp_number.valid }">
    <label for="whatsapp_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.market.columns.whatsapp_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.whatsapp_number" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('whatsapp_number'), 'form-control-success': fields.whatsapp_number && fields.whatsapp_number.valid}" id="whatsapp_number" name="whatsapp_number" placeholder="{{ trans('admin.market.columns.whatsapp_number') }}">
        <div v-if="errors.has('whatsapp_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('whatsapp_number') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('email_address'), 'has-success': fields.email_address && fields.email_address.valid }">
    <label for="email_address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.market.columns.email_address') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.email_address" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('email_address'), 'form-control-success': fields.email_address && fields.email_address.valid}" id="email_address" name="email_address" placeholder="{{ trans('admin.market.columns.email_address') }}">
        <div v-if="errors.has('email_address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email_address') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="order_index" type="checkbox" v-model="form.order_index" v-validate="''" data-vv-name="order_index"  name="order_index_fake_element">
        <label class="form-check-label" for="order_index">
            {{ trans('admin.market.columns.order_index') }}
        </label>
        <input type="hidden" name="order_index" :value="form.order_index">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>


