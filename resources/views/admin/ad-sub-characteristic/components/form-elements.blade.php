<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_id'), 'has-success': fields.ad_id && fields.ad_id.valid }">
    <label for="ad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.ad-sub-characteristic.columns.ad_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_id'), 'form-control-success': fields.ad_id && fields.ad_id.valid}" id="ad_id" name="ad_id" placeholder="{{ trans('admin.ad-sub-characteristic.columns.ad_id') }}">
        <div v-if="errors.has('ad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('sub_characteristic_id'), 'has-success': fields.sub_characteristic_id && fields.sub_characteristic_id.valid }">
    <label for="sub_characteristic_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.ad-sub-characteristic.columns.sub_characteristic_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.sub_characteristic_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sub_characteristic_id'), 'form-control-success': fields.sub_characteristic_id && fields.sub_characteristic_id.valid}" id="sub_characteristic_id" name="sub_characteristic_id" placeholder="{{ trans('admin.ad-sub-characteristic.columns.sub_characteristic_id') }}">
        <div v-if="errors.has('sub_characteristic_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sub_characteristic_id') }}</div>
    </div>
</div>


