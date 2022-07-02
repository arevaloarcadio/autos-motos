<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsvAd extends Model
{
	use \App\Traits\TraitUuid;
	
	protected $appends = ['resource_url'];
	
	protected $fillable = [
        'name',
        'user_id'
    ];

	public function getResourceUrlAttribute()
    {
        return url('/admin/car-wheel-drive-types/'.$this->getKey());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ad()
    {
        return $this->hasOne(Ad::class,'csv_ad_id');
    }

}