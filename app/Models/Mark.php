<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;
    
    protected $table = "makes";

    /**
     * @return HasMany
     */
    public function sub_models()
    {
        return $this->hasMany(Submodel::class,'make_id')->orderBy('name', 'ASC');
    }
}
