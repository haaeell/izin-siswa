<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentPermissionCheckin extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_permission_id',
        'checkin_at',
        'status',
    ];

    protected $casts = [
        'checkin_at' => 'datetime',
    ];

    public function permission()
    {
        return $this->belongsTo(StudentPermission::class, 'student_permission_id');
    }
}
