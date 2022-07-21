<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.sub-characteristic.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.sub-characteristic.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('characteristic_id'), 'has-success': fields.characteristic_id && fields.characteristic_id.valid }">
    <label for="characteristic_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.sub-characteristic.columns.characteristic_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.characteristic_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('characteristic_id'), 'form-control-success': fields.characteristic_id && fields.characteristic_id.valid}" id="characteristic_id" name="characteristic_id" placeholder="{{ trans('admin.sub-characteristic.columns.characteristic_id') }}">
        <div v-if="errors.has('characteristic_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('characteristic_id') }}</div>
    </div>
</div>


