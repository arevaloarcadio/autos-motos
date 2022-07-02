<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectedComment extends Model
{	
	use \App\Traits\TraitUuid;

	
	protected $table = 'rejected_comments';

	protected $fillable =  ['id', 'comment'];
    
    use HasFactory;


}
