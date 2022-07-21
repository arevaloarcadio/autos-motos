<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mount'), 'has-success': fields.mount && fields.mount.valid }">
    <label for="mount" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.payment-history.columns.mount') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mount" v-validate="'required|decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mount'), 'form-control-success': fields.mount && fields.mount.valid}" id="mount" name="mount" placeholder="{{ trans('admin.payment-history.columns.mount') }}">
        <div v-if="errors.has('mount')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mount') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('data'), 'has-success': fields.data && fields.data.valid }">
    <label for="data" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.payment-history.columns.data') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.data" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('data'), 'form-control-success': fields.data && fields.data.valid}" id="data" name="data" placeholder="{{ trans('admin.payment-history.columns.data') }}">
        <div v-if="errors.has('data')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('data') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('way_to_pay'), 'has-success': fields.way_to_pay && fields.way_to_pay.valid }">
    <label for="way_to_pay" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.payment-history.columns.way_to_pay') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.way_to_pay" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('way_to_pay'), 'form-control-success': fields.way_to_pay && fields.way_to_pay.valid}" id="way_to_pay" name="way_to_pay" placeholder="{{ trans('admin.payment-history.columns.way_to_pay') }}">
        <div v-if="errors.has('way_to_pay')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('way_to_pay') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('transaction_number'), 'has-success': fields.transaction_number && fields.transaction_number.valid }">
    <label for="transaction_number" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.payment-history.columns.transaction_number') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.transaction_number" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('transaction_number'), 'form-control-success': fields.transaction_number && fields.transaction_number.valid}" id="transaction_number" name="transaction_number" placeholder="{{ trans('admin.payment-history.columns.transaction_number') }}">
        <div v-if="errors.has('transaction_number')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('transaction_number') }}</div>
    </div>
</div>


