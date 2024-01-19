<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['product_id', 'name'];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
