<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use CrudTrait;
    use HasFactory;
    protected $fillable = [
        'book_name',
        'book_isbn',
        'book_publish_year',
        'book_cover',
    ];

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            if($obj->book_cover){
                Storage::disk('public')->delete($obj->book_cover);
            }
        });
    }

    public function setBookNameAttribute($value)
    {
        $this->attributes['book_name'] = ucwords($value);
    }

    public function bookStock(){
        return $this->hasMany(BookStock::class);
    }
}
