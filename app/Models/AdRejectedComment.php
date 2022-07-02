<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AdRejectedComment extends Pivot
{
	use \App\Traits\TraitUuid;

	protected $table = 'ad_rejected_comments';
	
    protected $fillable =  ['id', 'rejected_comment_id','ad_id'];
}
