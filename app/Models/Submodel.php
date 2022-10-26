<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submodel extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;

    protected $table = "sub_models";

    public function sub_models_by_model(){
        return $this->belongsToMany(Models::class,'sub_model_by_models','sub_model_id','model_id')
                  ->using(SubmodelByModel::class);
    }
}
