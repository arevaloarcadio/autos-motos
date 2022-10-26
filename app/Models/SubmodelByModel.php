<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SubmodelByModel extends Pivot
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = "sub_model_by_models";

    public function models()
    {
        return $this->hasMany(Submodel::class,'make_id')->orderBy('name', 'ASC');
    }
}
