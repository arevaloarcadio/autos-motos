<div class="form-group row align-items-center" :class="{'has-danger': errors.has('plan_id'), 'has-success': fields.plan_id && fields.plan_id.valid }">
    <label for="plan_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.receipt.columns.plan_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.plan_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('plan_id'), 'form-control-success': fields.plan_id && fields.plan_id.valid}" id="plan_id" name="plan_id" placeholder="{{ trans('admin.receipt.columns.plan_id') }}">
        <div v-if="errors.has('plan_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('plan_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('user_id'), 'has-success': fields.user_id && fields.user_id.valid }">
    <label for="user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.receipt.columns.user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.user_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('user_id'), 'form-control-success': fields.user_id && fields.user_id.valid}" id="user_id" name="user_id" placeholder="{{ trans('admin.receipt.columns.user_id') }}">
        <div v-if="errors.has('user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('file'), 'has-success': fields.file && fields.file.valid }">
    <label for="file" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.receipt.columns.file') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.file" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('file'), 'form-control-success': fields.file && fields.file.valid}" id="file" name="file" placeholder="{{ trans('admin.receipt.columns.file') }}">
        <div v-if="errors.has('file')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('file') }}</div>
    </div>
</div>


