<div class="form-group row align-items-center" :class="{'has-danger': errors.has('vehicle_ads'), 'has-success': fields.vehicle_ads && fields.vehicle_ads.valid }">
    <label for="vehicle_ads" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-promotion-plan.columns.vehicle_ads') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.vehicle_ads" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('vehicle_ads'), 'form-control-success': fields.vehicle_ads && fields.vehicle_ads.valid}" id="vehicle_ads" name="vehicle_ads" placeholder="{{ trans('admin.characteristic-promotion-plan.columns.vehicle_ads') }}">
        <div v-if="errors.has('vehicle_ads')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('vehicle_ads') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('shop_ads'), 'has-success': fields.shop_ads && fields.shop_ads.valid }">
    <label for="shop_ads" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-promotion-plan.columns.shop_ads') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.shop_ads" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('shop_ads'), 'form-control-success': fields.shop_ads && fields.shop_ads.valid}" id="shop_ads" name="shop_ads" placeholder="{{ trans('admin.characteristic-promotion-plan.columns.shop_ads') }}">
        <div v-if="errors.has('shop_ads')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('shop_ads') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('rental_ads'), 'has-success': fields.rental_ads && fields.rental_ads.valid }">
    <label for="rental_ads" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-promotion-plan.columns.rental_ads') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.rental_ads" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('rental_ads'), 'form-control-success': fields.rental_ads && fields.rental_ads.valid}" id="rental_ads" name="rental_ads" placeholder="{{ trans('admin.characteristic-promotion-plan.columns.rental_ads') }}">
        <div v-if="errors.has('rental_ads')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('rental_ads') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mechanic_ads'), 'has-success': fields.mechanic_ads && fields.mechanic_ads.valid }">
    <label for="mechanic_ads" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-promotion-plan.columns.mechanic_ads') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mechanic_ads" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mechanic_ads'), 'form-control-success': fields.mechanic_ads && fields.mechanic_ads.valid}" id="mechanic_ads" name="mechanic_ads" placeholder="{{ trans('admin.characteristic-promotion-plan.columns.mechanic_ads') }}">
        <div v-if="errors.has('mechanic_ads')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mechanic_ads') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('front_page_promotion'), 'has-success': fields.front_page_promotion && fields.front_page_promotion.valid }">
    <label for="front_page_promotion" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-promotion-plan.columns.front_page_promotion') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.front_page_promotion" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('front_page_promotion'), 'form-control-success': fields.front_page_promotion && fields.front_page_promotion.valid}" id="front_page_promotion" name="front_page_promotion" placeholder="{{ trans('admin.characteristic-promotion-plan.columns.front_page_promotion') }}">
        <div v-if="errors.has('front_page_promotion')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('front_page_promotion') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('plan_id'), 'has-success': fields.plan_id && fields.plan_id.valid }">
    <label for="plan_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.characteristic-promotion-plan.columns.plan_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.plan_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('plan_id'), 'form-control-success': fields.plan_id && fields.plan_id.valid}" id="plan_id" name="plan_id" placeholder="{{ trans('admin.characteristic-promotion-plan.columns.plan_id') }}">
        <div v-if="errors.has('plan_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('plan_id') }}</div>
    </div>
</div>


