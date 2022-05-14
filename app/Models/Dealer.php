<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
     use \App\Traits\TraitUuid;
      use \App\Traits\Relationships;
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

    /**
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany
     */
    public function showRooms()
    {
        return $this->hasMany(DealerShowRoom::class)->orderBy('name', 'ASC');
    }

    /**
     * @param string|null $value
     *
     * @return string|null
     */
    /*public function getLogoPathAttribute(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return Storage::disk('s3')->url($value);
    }*/
}
