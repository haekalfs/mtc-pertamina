<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit_log extends Model
{
    use HasFactory;
    protected $table = "audit_log";
    protected $fillable = ["id", "description", 'priority', 'user_id', 'log_id',"created_at", "updated_at"];

    /**
     * Create a new audit log entry
     *
     * @param string $description
     * @param string $priority
     * @param int $user_id
     * @param int|null $log_id
     * @return Audit_log
     */
    public static function createLog($description,$priority,$user_id, ?int $log_id = null)
    {
        return self::create([
            'description' => $description,
            'priority' => $priority,
            'user_id' => $user_id,
            'log_id' => $log_id,
        ]);
    }

    public function user(){
    	return $this->belongsTo('App\Models\User')->withDefault();
    }
}
