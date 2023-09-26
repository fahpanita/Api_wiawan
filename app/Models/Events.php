<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    public function sub()
    {
        return $this->hasMany(Events::class, 'parent_id', 'id');
    }
}
