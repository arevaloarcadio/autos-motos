<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_id'), 'has-success': fields.ad_id && fields.ad_id.valid }">
    <label for="ad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.ad-image.columns.ad_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_id'), 'form-control-success': fields.ad_id && fields.ad_id.valid}" id="ad_id" name="ad_id" placeholder="{{ trans('admin.ad-image.columns.ad_id') }}">
        <div v-if="errors.has('ad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('path'), 'has-success': fields.path && fields.path.valid }">
    <label for="path" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.ad-image.columns.path') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.path" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('path'), 'form-control-success': fields.path && fields.path.valid}" id="path" name="path" placeholder="{{ trans('admin.ad-image.columns.path') }}">
        <div v-if="errors.has('path')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('path') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('is_external'), 'has-success': fields.is_external && fields.is_external.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_external" type="checkbox" v-model="form.is_external" v-validate="''" data-vv-name="is_external"  name="is_external_fake_element">
        <label class="form-check-label" for="is_external">
            {{ trans('admin.ad-image.columns.is_external') }}
        </label>
        <input type="hidden" name="is_external" :value="form.is_external">
        <div v-if="errors.has('is_external')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_external') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="order_index" type="checkbox" v-model="form.order_index" v-validate="''" data-vv-name="order_index"  name="order_index_fake_element">
        <label class="form-check-label" for="order_index">
            {{ trans('admin.ad-image.columns.order_index') }}
        </label>
        <input type="hidden" name="order_index" :value="form.order_index">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>


