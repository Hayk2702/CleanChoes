<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    public function services()
    {
        return $this->hasMany(Service::class, 'category_id', 'id');
    }

    public function workPhotos()
    {
        return $this->hasMany(WorkPhotos::class, 'category_id', 'id');
    }
}
