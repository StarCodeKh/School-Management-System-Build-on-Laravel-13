<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use App\Models\Concerns\HasDisplayCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasDisplayCode, HasFactory, Notifiable;

    public const DISPLAY_CODE_COLUMN = 'user_id';

    public const DISPLAY_CODE_PREFIX = 'USR-';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'date_of_birth',
        'join_date',
        'phone_number',
        'status',
        'role_name',
        'avatar',
        'position',
        'department',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'join_date' => 'date',
            'password' => 'hashed',
            'role_name' => Role::class,
        ];
    }

    public function hasRole(Role ...$roles): bool
    {
        return in_array($this->role_name, $roles, true);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->role_name?->isAdmin();
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    public function feeCollections(): HasMany
    {
        return $this->hasMany(FeeCollection::class, 'student_id');
    }
}
