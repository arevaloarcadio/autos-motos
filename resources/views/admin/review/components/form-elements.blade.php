<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ad_id'), 'has-success': fields.ad_id && fields.ad_id.valid }">
    <label for="ad_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.review.columns.ad_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ad_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ad_id'), 'form-control-success': fields.ad_id && fields.ad_id.valid}" id="ad_id" name="ad_id" placeholder="{{ trans('admin.review.columns.ad_id') }}">
        <div v-if="errors.has('ad_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ad_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('testimony'), 'has-success': fields.testimony && fields.testimony.valid }">
    <label for="testimony" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.review.columns.testimony') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <textarea class="form-control" v-model="form.testimony" v-validate="'required'" id="testimony" name="testimony"></textarea>
        </div>
        <div v-if="errors.has('testimony')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('testimony') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.review.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.review.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('score'), 'has-success': fields.score && fields.score.valid }">
    <label for="score" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.review.columns.score') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.score" v-validate="'required|decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('score'), 'form-control-success': fields.score && fields.score.valid}" id="score" name="score" placeholder="{{ trans('admin.review.columns.score') }}">
        <div v-if="errors.has('score')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('score') }}</div>
    </div>
</div>


