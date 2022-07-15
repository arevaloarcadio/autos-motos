<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'mount',
        'data',
        'way_to_pay',
        'transaction_number',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/payment-histories/'.$this->getKey());
    }
}
