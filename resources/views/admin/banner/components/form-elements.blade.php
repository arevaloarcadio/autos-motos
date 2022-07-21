<div class="form-group row align-items-center" :class="{'has-danger': errors.has('location'), 'has-success': fields.location && fields.location.valid }">
    <label for="location" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.banner.columns.location') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.location" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('location'), 'form-control-success': fields.location && fields.location.valid}" id="location" name="location" placeholder="{{ trans('admin.banner.columns.location') }}">
        <div v-if="errors.has('location')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('location') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('image_path'), 'has-success': fields.image_path && fields.image_path.valid }">
    <label for="image_path" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.banner.columns.image_path') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.image_path" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('image_path'), 'form-control-success': fields.image_path && fields.image_path.valid}" id="image_path" name="image_path" placeholder="{{ trans('admin.banner.columns.image_path') }}">
        <div v-if="errors.has('image_path')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('image_path') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('link'), 'has-success': fields.link && fields.link.valid }">
    <label for="link" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.banner.columns.link') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.link" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('link'), 'form-control-success': fields.link && fields.link.valid}" id="link" name="link" placeholder="{{ trans('admin.banner.columns.link') }}">
        <div v-if="errors.has('link')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('link') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="order_index" type="checkbox" v-model="form.order_index" v-validate="''" data-vv-name="order_index"  name="order_index_fake_element">
        <label class="form-check-label" for="order_index">
            {{ trans('admin.banner.columns.order_index') }}
        </label>
        <input type="hidden" name="order_index" :value="form.order_index">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>


