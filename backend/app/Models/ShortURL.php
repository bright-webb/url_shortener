<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShortURL extends Model
{
    use HasFactory;
    protected $fillable = [
        'original_url',
        'short_code',
    ];

}
