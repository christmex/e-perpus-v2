<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['publisher_name'];

    public function setPublisherNameAttribute($value)
    {
        $this->attributes['publisher_name'] = ucwords($value);
    }
}
