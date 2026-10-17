<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function feeCollections(): HasMany
    {
        return $this->hasMany(FeeCollection::class);
    }
}
