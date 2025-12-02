<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPhotos extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(\App\Models\Categories::class, 'category_id');
    }
}

