<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'date_of_birth',
        'qualification',
        'experience',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'experience' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
