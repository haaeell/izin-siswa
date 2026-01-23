<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'wali_kelas_id',
        'approved_by',
        'type',
        'start_at',
        'end_at',
        'reason',
        'status',
        'reject_reason',
        'qr_token',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function checkin()
    {
        return $this->hasMany(StudentPermissionCheckin::class);
    }
}
