<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'class_id',
        'dormitory_id',
    ];

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function permissions()
    {
        return $this->hasMany(StudentPermission::class);
    }
}
