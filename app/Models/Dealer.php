<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    protected $fillable = [
        'slug',
        'company_name',
        'vat_number',
        'address',
        'zip_code',
        'city',
        'country',
        'logo_path',
        'email_address',
        'phone_number',
        'status',
        'description',
        'external_id',
        'source',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/dealers/'.$this->getKey());
    }
}
