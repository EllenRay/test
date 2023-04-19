<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $filable = [
      'no_employee',
      'branch_id',
      'role_id'
	];

  public function branch()
  {
    return $this->belongsTo('App\Models\Branch','branch_id');
  }

  public function role()
  {
    return $this->belongsTo('App\Models\Role','role_id');
  }
}
