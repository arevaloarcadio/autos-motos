<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserPlan extends Pivot
{
	protected $table = 'user_plans';

	use \App\Traits\TraitUuid;
    use \App\Traits\Relationships;
      
    use HasFactory;
}
