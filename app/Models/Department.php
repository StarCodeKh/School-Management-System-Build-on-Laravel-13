<?php

namespace App\Models;

use App\Models\Concerns\HasDisplayCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasDisplayCode, HasFactory;

    public const DISPLAY_CODE_COLUMN = 'department_id';

    public const DISPLAY_CODE_PREFIX = 'DEP-';

    protected $fillable = [
        'department_name',
        'head_of_department',
        'department_start_date',
        'no_of_students',
    ];

    protected function casts(): array
    {
        return [
            'department_start_date' => 'date',
            'no_of_students' => 'integer',
        ];
    }
}
