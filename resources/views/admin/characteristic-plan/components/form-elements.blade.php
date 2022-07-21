<div class="form-group row align-items-center" :class="{'has-danger': errors.has('vehicle_ads'), 'has-success': fields.vehicle_ads && fields.vehicle_ads.valid }">
    <label for="vehicle_ads" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-plan.columns.vehicle_ads') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.vehicle_ads" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('vehicle_ads'), 'form-control-success': fields.vehicle_ads && fields.vehicle_ads.valid}" id="vehicle_ads" name="vehicle_ads" placeholder="{{ trans('admin.characteristic-plan.columns.vehicle_ads') }}">
        <div v-if="errors.has('vehicle_ads')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('vehicle_ads') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('rental_ads'), 'has-success': fields.rental_ads && fields.rental_ads.valid }">
    <label for="rental_ads" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-plan.columns.rental_ads') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.rental_ads" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('rental_ads'), 'form-control-success': fields.rental_ads && fields.rental_ads.valid}" id="rental_ads" name="rental_ads" placeholder="{{ trans('admin.characteristic-plan.columns.rental_ads') }}">
        <div v-if="errors.has('rental_ads')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('rental_ads') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('promotion_month'), 'has-success': fields.promotion_month && fields.promotion_month.valid }">
    <label for="promotion_month" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-plan.columns.promotion_month') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.promotion_month" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('promotion_month'), 'form-control-success': fields.promotion_month && fields.promotion_month.valid}" id="promotion_month" name="promotion_month" placeholder="{{ trans('admin.characteristic-plan.columns.promotion_month') }}">
        <div v-if="errors.has('promotion_month')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('promotion_month') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('front_page_promotion'), 'has-success': fields.front_page_promotion && fields.front_page_promotion.valid }">
    <label for="front_page_promotion" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-plan.columns.front_page_promotion') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.front_page_promotion" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('front_page_promotion'), 'form-control-success': fields.front_page_promotion && fields.front_page_promotion.valid}" id="front_page_promotion" name="front_page_promotion" placeholder="{{ trans('admin.characteristic-plan.columns.front_page_promotion') }}">
        <div v-if="errors.has('front_page_promotion')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('front_page_promotion') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('video_a_day'), 'has-success': fields.video_a_day && fields.video_a_day.valid }">
    <label for="video_a_day" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-plan.columns.video_a_day') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.video_a_day" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('video_a_day'), 'form-control-success': fields.video_a_day && fields.video_a_day.valid}" id="video_a_day" name="video_a_day" placeholder="{{ trans('admin.characteristic-plan.columns.video_a_day') }}">
        <div v-if="errors.has('video_a_day')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('video_a_day') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mechanics_rental_ads'), 'has-success': fields.mechanics_rental_ads && fields.mechanics_rental_ads.valid }">
    <label for="mechanics_rental_ads" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-plan.columns.mechanics_rental_ads') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mechanics_rental_ads" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mechanics_rental_ads'), 'form-control-success': fields.mechanics_rental_ads && fields.mechanics_rental_ads.valid}" id="mechanics_rental_ads" name="mechanics_rental_ads" placeholder="{{ trans('admin.characteristic-plan.columns.mechanics_rental_ads') }}">
        <div v-if="errors.has('mechanics_rental_ads')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mechanics_rental_ads') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('plan_id'), 'has-success': fields.plan_id && fields.plan_id.valid }">
    <label for="plan_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-plan.columns.plan_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.plan_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('plan_id'), 'form-control-success': fields.plan_id && fields.plan_id.valid}" id="plan_id" name="plan_id" placeholder="{{ trans('admin.characteristic-plan.columns.plan_id') }}">
        <div v-if="errors.has('plan_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('plan_id') }}</div>
    </div>
</div>


