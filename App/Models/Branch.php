<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
	use HasFactory;

	protected $filable = [
		'no_branch',
	];

	public function employees()
	{
		return $this->hasMany('App\Models\Employee');
	}
}
