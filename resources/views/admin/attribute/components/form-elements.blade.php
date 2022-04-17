<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.attribute.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.attribute.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('searched'), 'has-success': fields.searched && fields.searched.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="searched" type="checkbox" v-model="form.searched" v-validate="''" data-vv-name="searched"  name="searched_fake_element">
        <label class="form-check-label" for="searched">
            {{ trans('admin.attribute.columns.searched') }}
        </label>
        <input type="hidden" name="searched" :value="form.searched">
        <div v-if="errors.has('searched')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('searched') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('featured'), 'has-success': fields.featured && fields.featured.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="featured" type="checkbox" v-model="form.featured" v-validate="''" data-vv-name="featured"  name="featured_fake_element">
        <label class="form-check-label" for="featured">
            {{ trans('admin.attribute.columns.featured') }}
        </label>
        <input type="hidden" name="featured" :value="form.featured">
        <div v-if="errors.has('featured')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('featured') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('is_choice'), 'has-success': fields.is_choice && fields.is_choice.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_choice" type="checkbox" v-model="form.is_choice" v-validate="''" data-vv-name="is_choice"  name="is_choice_fake_element">
        <label class="form-check-label" for="is_choice">
            {{ trans('admin.attribute.columns.is_choice') }}
        </label>
        <input type="hidden" name="is_choice" :value="form.is_choice">
        <div v-if="errors.has('is_choice')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_choice') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('order_level'), 'has-success': fields.order_level && fields.order_level.valid }">
    <label for="order_level" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.attribute.columns.order_level') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.order_level" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('order_level'), 'form-control-success': fields.order_level && fields.order_level.valid}" id="order_level" name="order_level" placeholder="{{ trans('admin.attribute.columns.order_level') }}">
        <div v-if="errors.has('order_level')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_level') }}</div>
    </div>
</div>


