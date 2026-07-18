<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'role', 'job_title', 'password', 'school_id', 'is_hired'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    const ROLE_TEACHER = 'teacher';
    const ROLE_SCHOOL = 'school';
    const ROLE_ADMIN = 'admin';
    const ROLE_EMPLOYEE = 'employee';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function isAdmin(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->role === self::ROLE_ADMIN,
        );
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isSchool(): bool
    {
        return $this->role === self::ROLE_SCHOOL;
    }

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class)->withTimestamps();
    }

    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function schoolProfile()
    {
        return $this->hasOne(SchoolProfile::class);
    }

    public function sentEmployments()
    {
        return $this->hasMany(Employment::class, 'school_id');
    }

    public function hiredEmployments()
    {
        return $this->hasMany(Employment::class, 'school_id')->where('status', 'hired');
    }

    public function school()
    {
        return $this->belongsTo(User::class, 'school_id');
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'purchase_user')->withTimestamps();
    }

    public function userBalance()
    {
        return $this->hasOne(UserBalance::class);
    }

    public function balanceTransactions()
    {
        return $this->hasMany(BalanceTransaction::class);
    }

    public function effectiveSchoolId(): int
    {
        return $this->school_id ?? $this->id;
    }
}
