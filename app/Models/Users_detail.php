<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users_detail extends Model
{
    use HasFactory;
    protected $table = 'users_details';
    protected $fillable = ['user_id','employment_status', 'position_id', 'department_id',
    'employee_id','profile_pic'];

    public function department()
    {
        return $this->belongsTo('App\Models\Department')->withDefault();
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Position')->withDefault();
    }
}
