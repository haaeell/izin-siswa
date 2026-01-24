<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function class()
    {
        return $this->hasOne(SchoolClass::class, 'wali_kelas_id');
    }

    public function submittedPermissions()
    {
        return $this->hasMany(StudentPermission::class, 'wali_kelas_id');
    }

    public function approvedPermissions()
    {
        return $this->hasMany(StudentPermission::class, 'approved_by');
    }
}
