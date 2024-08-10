<?php

namespace App\Models;

use App\Traits\UUIDAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory, UUIDAsPrimaryKey;

    protected $fillable = ['name'];

    // relationship
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
