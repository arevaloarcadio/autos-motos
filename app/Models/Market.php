<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'internal_name',
        'slug',
        'domain',
        'default_locale_id',
        'icon',
        'mobile_number',
        'whatsapp_number',
        'email_address',
        'order_index',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/markets/'.$this->getKey());
    }

    public function defaultLocale()
    {
        return $this->belongsTo(Locale::class, 'default_locale_id', 'id');
    }

    /**
     * @param string|null $value
     *
     * @return string|null
     */
   /* public function getIconAttribute(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return Storage::disk('s3')->url($value);
    }*/

    /**
     * @return string
     */
    protected function getSluggableField(): string
    {
        return 'internal_name';
    }
}
