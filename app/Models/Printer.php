<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'characters_per_line',
        'path',
        'ip_address',
        'port',
    ];

}
