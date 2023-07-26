<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['author_name'];

    public function setAuthorNameAttribute($value)
    {
        $this->attributes['author_name'] = ucwords($value);
    }
}
