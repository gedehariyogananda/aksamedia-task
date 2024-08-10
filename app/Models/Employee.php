<?php

namespace App\Models;

use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, UUIDAsPrimaryKey;

    protected $fillable = [
        'name',
        'image',
        'phone',
        'position',
        'division_id'
    ];

    // relationship
    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
