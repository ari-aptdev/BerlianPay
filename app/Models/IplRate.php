<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IplRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_type',
        'nominal',
        'effective_date',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'nominal' => 'integer',
        ];
    }
}
