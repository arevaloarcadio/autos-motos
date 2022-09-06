<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AdSubCharacteristic extends Pivot
{
        use \App\Traits\TraitUuid;
    protected $fillable = [
        'ad_id',
        'sub_characteristic_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    
}
