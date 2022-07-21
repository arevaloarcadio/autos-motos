<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;

    public function items()
    {
        return $this->hasMany(ItemPlan::class, 'plan_id');
    }

    public function characteristic_plans()
    {
        return $this->hasMany(CharacteristicPlan::class, 'plan_id');
    }

    public function characteristic_promotion_plans()
    {
        return $this->hasMany(CharacteristicPromotionPlan::class, 'plan_id');
    }
}
