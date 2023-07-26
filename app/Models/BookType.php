<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookType extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['book_type_name'];

    public function setBookTypeNameAttribute($value)
    {
        $this->attributes['book_type_name'] = ucwords($value);
    }
}
