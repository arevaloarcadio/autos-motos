<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
    protected $fillable = [
        'internal_name',
        'code',
        'icon',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/locales/'.$this->getKey());
    }

    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
    
    /**
     * @param string|null $value
     *
     * @return string|null
     */
    /*public function getIconAttribute(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }
        $file = Storage::disk('s3')->url($value);
        
        return $file;
    }*/
}
