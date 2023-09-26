<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catagories extends Model
{
    public function sub()
    {
        return $this->hasMany(Catagories::class, 'parent_id', 'id');
    }
}
