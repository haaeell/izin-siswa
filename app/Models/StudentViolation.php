<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentViolation extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'handling_type',
        'description',

        'no_phone',
        'no_permission',
        'no_phone_until',
        'no_permission_until',

        'attendance_percentage',
        'attendance_until',

        'occurred_at',
        'reported_by',
    ];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
