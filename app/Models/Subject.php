<?php

namespace App\Models;

use App\Models\Concerns\HasDisplayCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasDisplayCode, HasFactory;

    public const DISPLAY_CODE_COLUMN = 'subject_id';

    public const DISPLAY_CODE_PREFIX = 'SUB-';

    protected $fillable = [
        'subject_name',
        'class',
    ];
}
