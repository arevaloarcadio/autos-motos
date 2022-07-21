<div class="form-group row align-items-center" :class="{'has-danger': errors.has('car_make_id'), 'has-success': fields.car_make_id && fields.car_make_id.valid }">
    <label for="car_make_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.car_make_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.car_make_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('car_make_id'), 'form-control-success': fields.car_make_id && fields.car_make_id.valid}" id="car_make_id" name="car_make_id" placeholder="{{ trans('admin.car-spec.columns.car_make_id') }}">
        <div v-if="errors.has('car_make_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('car_make_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('production_end_year'), 'has-success': fields.production_end_year && fields.production_end_year.valid }">
    <label for="production_end_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.production_end_year') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-sm-8'">
        <div class="input-group input-group--custom">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <datetime v-model="form.production_end_year" :config="datePickerConfig" v-validate="'date_format:yyyy-MM-dd HH:mm:ss'" class="flatpickr" :class="{'form-control-danger': errors.has('production_end_year'), 'form-control-success': fields.production_end_year && fields.production_end_year.valid}" id="production_end_year" name="production_end_year" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_a_date') }}"></datetime>
        </div>
        <div v-if="errors.has('production_end_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('production_end_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('external_id'), 'has-success': fields.external_id && fields.external_id.valid }">
    <label for="external_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.external_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.external_id" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('external_id'), 'form-control-success': fields.external_id && fields.external_id.valid}" id="external_id" name="external_id" placeholder="{{ trans('admin.car-spec.columns.external_id') }}">
        <div v-if="errors.has('external_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('external_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('electric_power_rpm_max'), 'has-success': fields.electric_power_rpm_max && fields.electric_power_rpm_max.valid }">
    <label for="electric_power_rpm_max" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.electric_power_rpm_max') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.electric_power_rpm_max" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('electric_power_rpm_max'), 'form-control-success': fields.electric_power_rpm_max && fields.electric_power_rpm_max.valid}" id="electric_power_rpm_max" name="electric_power_rpm_max" placeholder="{{ trans('admin.car-spec.columns.electric_power_rpm_max') }}">
        <div v-if="errors.has('electric_power_rpm_max')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('electric_power_rpm_max') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('electric_power_rpm_min'), 'has-success': fields.electric_power_rpm_min && fields.electric_power_rpm_min.valid }">
    <label for="electric_power_rpm_min" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.electric_power_rpm_min') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.electric_power_rpm_min" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('electric_power_rpm_min'), 'form-control-success': fields.electric_power_rpm_min && fields.electric_power_rpm_min.valid}" id="electric_power_rpm_min" name="electric_power_rpm_min" placeholder="{{ trans('admin.car-spec.columns.electric_power_rpm_min') }}">
        <div v-if="errors.has('electric_power_rpm_min')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('electric_power_rpm_min') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('electric_power_rpm'), 'has-success': fields.electric_power_rpm && fields.electric_power_rpm.valid }">
    <label for="electric_power_rpm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.electric_power_rpm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.electric_power_rpm" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('electric_power_rpm'), 'form-control-success': fields.electric_power_rpm && fields.electric_power_rpm.valid}" id="electric_power_rpm" name="electric_power_rpm" placeholder="{{ trans('admin.car-spec.columns.electric_power_rpm') }}">
        <div v-if="errors.has('electric_power_rpm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('electric_power_rpm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('electric_power_hp'), 'has-success': fields.electric_power_hp && fields.electric_power_hp.valid }">
    <label for="electric_power_hp" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.electric_power_hp') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.electric_power_hp" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('electric_power_hp'), 'form-control-success': fields.electric_power_hp && fields.electric_power_hp.valid}" id="electric_power_hp" name="electric_power_hp" placeholder="{{ trans('admin.car-spec.columns.electric_power_hp') }}">
        <div v-if="errors.has('electric_power_hp')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('electric_power_hp') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('battery_capacity'), 'has-success': fields.battery_capacity && fields.battery_capacity.valid }">
    <label for="battery_capacity" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.battery_capacity') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.battery_capacity" v-validate="'decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('battery_capacity'), 'form-control-success': fields.battery_capacity && fields.battery_capacity.valid}" id="battery_capacity" name="battery_capacity" placeholder="{{ trans('admin.car-spec.columns.battery_capacity') }}">
        <div v-if="errors.has('battery_capacity')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('battery_capacity') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('car_wheel_drive_type_id'), 'has-success': fields.car_wheel_drive_type_id && fields.car_wheel_drive_type_id.valid }">
    <label for="car_wheel_drive_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.car_wheel_drive_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.car_wheel_drive_type_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('car_wheel_drive_type_id'), 'form-control-success': fields.car_wheel_drive_type_id && fields.car_wheel_drive_type_id.valid}" id="car_wheel_drive_type_id" name="car_wheel_drive_type_id" placeholder="{{ trans('admin.car-spec.columns.car_wheel_drive_type_id') }}">
        <div v-if="errors.has('car_wheel_drive_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('car_wheel_drive_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('gears'), 'has-success': fields.gears && fields.gears.valid }">
    <label for="gears" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.gears') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.gears" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('gears'), 'form-control-success': fields.gears && fields.gears.valid}" id="gears" name="gears" placeholder="{{ trans('admin.car-spec.columns.gears') }}">
        <div v-if="errors.has('gears')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('gears') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('car_transmission_type_id'), 'has-success': fields.car_transmission_type_id && fields.car_transmission_type_id.valid }">
    <label for="car_transmission_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.car_transmission_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.car_transmission_type_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('car_transmission_type_id'), 'form-control-success': fields.car_transmission_type_id && fields.car_transmission_type_id.valid}" id="car_transmission_type_id" name="car_transmission_type_id" placeholder="{{ trans('admin.car-spec.columns.car_transmission_type_id') }}">
        <div v-if="errors.has('car_transmission_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('car_transmission_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('car_fuel_type_id'), 'has-success': fields.car_fuel_type_id && fields.car_fuel_type_id.valid }">
    <label for="car_fuel_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.car_fuel_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.car_fuel_type_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('car_fuel_type_id'), 'form-control-success': fields.car_fuel_type_id && fields.car_fuel_type_id.valid}" id="car_fuel_type_id" name="car_fuel_type_id" placeholder="{{ trans('admin.car-spec.columns.car_fuel_type_id') }}">
        <div v-if="errors.has('car_fuel_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('car_fuel_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('production_start_year'), 'has-success': fields.production_start_year && fields.production_start_year.valid }">
    <label for="production_start_year" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.production_start_year') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-sm-8'">
        <div class="input-group input-group--custom">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <datetime v-model="form.production_start_year" :config="datePickerConfig" v-validate="'date_format:yyyy-MM-dd HH:mm:ss'" class="flatpickr" :class="{'form-control-danger': errors.has('production_start_year'), 'form-control-success': fields.production_start_year && fields.production_start_year.valid}" id="production_start_year" name="production_start_year" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_a_date') }}"></datetime>
        </div>
        <div v-if="errors.has('production_start_year')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('production_start_year') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('car_model_id'), 'has-success': fields.car_model_id && fields.car_model_id.valid }">
    <label for="car_model_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.car_model_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.car_model_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('car_model_id'), 'form-control-success': fields.car_model_id && fields.car_model_id.valid}" id="car_model_id" name="car_model_id" placeholder="{{ trans('admin.car-spec.columns.car_model_id') }}">
        <div v-if="errors.has('car_model_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('car_model_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('engine_displacement'), 'has-success': fields.engine_displacement && fields.engine_displacement.valid }">
    <label for="engine_displacement" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.engine_displacement') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.engine_displacement" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('engine_displacement'), 'form-control-success': fields.engine_displacement && fields.engine_displacement.valid}" id="engine_displacement" name="engine_displacement" placeholder="{{ trans('admin.car-spec.columns.engine_displacement') }}">
        <div v-if="errors.has('engine_displacement')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('engine_displacement') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('power_rpm_max'), 'has-success': fields.power_rpm_max && fields.power_rpm_max.valid }">
    <label for="power_rpm_max" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.power_rpm_max') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.power_rpm_max" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('power_rpm_max'), 'form-control-success': fields.power_rpm_max && fields.power_rpm_max.valid}" id="power_rpm_max" name="power_rpm_max" placeholder="{{ trans('admin.car-spec.columns.power_rpm_max') }}">
        <div v-if="errors.has('power_rpm_max')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('power_rpm_max') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('power_rpm_min'), 'has-success': fields.power_rpm_min && fields.power_rpm_min.valid }">
    <label for="power_rpm_min" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.power_rpm_min') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.power_rpm_min" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('power_rpm_min'), 'form-control-success': fields.power_rpm_min && fields.power_rpm_min.valid}" id="power_rpm_min" name="power_rpm_min" placeholder="{{ trans('admin.car-spec.columns.power_rpm_min') }}">
        <div v-if="errors.has('power_rpm_min')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('power_rpm_min') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('power_rpm'), 'has-success': fields.power_rpm && fields.power_rpm.valid }">
    <label for="power_rpm" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.power_rpm') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.power_rpm" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('power_rpm'), 'form-control-success': fields.power_rpm && fields.power_rpm.valid}" id="power_rpm" name="power_rpm" placeholder="{{ trans('admin.car-spec.columns.power_rpm') }}">
        <div v-if="errors.has('power_rpm')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('power_rpm') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('power_hp'), 'has-success': fields.power_hp && fields.power_hp.valid }">
    <label for="power_hp" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.power_hp') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.power_hp" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('power_hp'), 'form-control-success': fields.power_hp && fields.power_hp.valid}" id="power_hp" name="power_hp" placeholder="{{ trans('admin.car-spec.columns.power_hp') }}">
        <div v-if="errors.has('power_hp')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('power_hp') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('doors_max'), 'has-success': fields.doors_max && fields.doors_max.valid }">
    <label for="doors_max" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.doors_max') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.doors_max" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('doors_max'), 'form-control-success': fields.doors_max && fields.doors_max.valid}" id="doors_max" name="doors_max" placeholder="{{ trans('admin.car-spec.columns.doors_max') }}">
        <div v-if="errors.has('doors_max')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('doors_max') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('doors_min'), 'has-success': fields.doors_min && fields.doors_min.valid }">
    <label for="doors_min" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.doors_min') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.doors_min" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('doors_min'), 'form-control-success': fields.doors_min && fields.doors_min.valid}" id="doors_min" name="doors_min" placeholder="{{ trans('admin.car-spec.columns.doors_min') }}">
        <div v-if="errors.has('doors_min')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('doors_min') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('doors'), 'has-success': fields.doors && fields.doors.valid }">
    <label for="doors" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.doors') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.doors" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('doors'), 'form-control-success': fields.doors && fields.doors.valid}" id="doors" name="doors" placeholder="{{ trans('admin.car-spec.columns.doors') }}">
        <div v-if="errors.has('doors')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('doors') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('engine'), 'has-success': fields.engine && fields.engine.valid }">
    <label for="engine" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.engine') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.engine" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('engine'), 'form-control-success': fields.engine && fields.engine.valid}" id="engine" name="engine" placeholder="{{ trans('admin.car-spec.columns.engine') }}">
        <div v-if="errors.has('engine')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('engine') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('car_body_type_id'), 'has-success': fields.car_body_type_id && fields.car_body_type_id.valid }">
    <label for="car_body_type_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.car_body_type_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.car_body_type_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('car_body_type_id'), 'form-control-success': fields.car_body_type_id && fields.car_body_type_id.valid}" id="car_body_type_id" name="car_body_type_id" placeholder="{{ trans('admin.car-spec.columns.car_body_type_id') }}">
        <div v-if="errors.has('car_body_type_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('car_body_type_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('car_generation_id'), 'has-success': fields.car_generation_id && fields.car_generation_id.valid }">
    <label for="car_generation_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.car_generation_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.car_generation_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('car_generation_id'), 'form-control-success': fields.car_generation_id && fields.car_generation_id.valid}" id="car_generation_id" name="car_generation_id" placeholder="{{ trans('admin.car-spec.columns.car_generation_id') }}">
        <div v-if="errors.has('car_generation_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('car_generation_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('last_external_update'), 'has-success': fields.last_external_update && fields.last_external_update.valid }">
    <label for="last_external_update" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.car-spec.columns.last_external_update') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div class="input-group input-group--custom">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <datetime v-model="form.last_external_update" :config="datetimePickerConfig" v-validate="'date_format:yyyy-MM-dd HH:mm:ss'" class="flatpickr" :class="{'form-control-danger': errors.has('last_external_update'), 'form-control-success': fields.last_external_update && fields.last_external_update.valid}" id="last_external_update" name="last_external_update" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_date_and_time') }}"></datetime>
        </div>
        <div v-if="errors.has('last_external_update')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('last_external_update') }}</div>
    </div>
</div>


