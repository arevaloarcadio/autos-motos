<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable  implements JWTSubject
{
    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile_number',
        'landline_number',
        'whatsapp_number',
        'country_code_mobile_number',
        'country_code_whatsapp_number',
        'email',
        'email_verified_at',
        'password',
        'status',
        'type',
        'dealer_id',
        'image',
        'code_postal',
        'address',
        'country',
        'city'
    ];

    protected $hidden = [
        'password',
        'remember_token',

    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',

    ];

    protected $appends = ['review_ratings'];

    //protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    /*public function getResourceUrlAttribute()
    {
        return url('/admin/users/'.$this->getKey());
    }*/


    public function getReviewRatingsAttribute()
    {
        $score = Review::selectRaw('FORMAT(AVG(score),2) as ratings')
                ->join('ads','ads.id','reviews.ad_id')
                ->where('ads.user_id',$this->getKey())
                ->first();

        if ($score['ratings'] == null) {
            return 0;
        }

        return $score['ratings'];
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    /**
     * @return array
     */
    public function toAuthUserOutput(): array
    {
        $roles = collect($this->roles)->map(
            function (Role $role) {
                return $role->name;
            }
        );

        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'roles'      => $roles,
        ];
    }

    public function payment_histories()
    {
        return $this->hasMany(PaymentHistory::class, 'user_id');
    }

    public function plans(){
      return $this->belongsToMany(Plan::class,'user_plans')->using(UserPlan::class)
                  ->withPivot(['status','date_end_at']);
    }

    public function plan_active(){
      return $this->plans()->where('status','Aprobado');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
