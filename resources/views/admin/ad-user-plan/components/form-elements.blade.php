<div class="form-group row align-items-center" :class="{'has-danger': errors.has('plan_user_id'), 'has-success': fields.plan_user_id && fields.plan_user_id.valid }">
    <label for="plan_user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.ad-user-plan.columns.plan_user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.plan_user_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('plan_user_id'), 'form-control-success': fields.plan_user_id && fields.plan_user_id.valid}" id="plan_user_id" name="plan_user_id" placeholder="{{ trans('admin.ad-user-plan.columns.plan_user_id') }}">
        <div v-if="errors.has('plan_user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('plan_user_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_id'), 'has-success': fields.ad_id && fields.ad_id.valid }">
    <label for="ad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.ad-user-plan.columns.ad_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_id'), 'form-control-success': fields.ad_id && fields.ad_id.valid}" id="ad_id" name="ad_id" placeholder="{{ trans('admin.ad-user-plan.columns.ad_id') }}">
        <div v-if="errors.has('ad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_id') }}</div>
    </div>
</div>


