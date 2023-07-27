<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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

    public function getAllBookStockAttribute()
    {
        $stock = 0;
        foreach ($this->bookStock as $key => $value) {
            $stock += $value->book_stock_qty;
        }
        foreach ($this->transactions as $key => $value) {
            $stock += $value->transaction_book_qty;
        }
        return $stock;
        
    }

    public function bookStock(){
        return $this->hasMany(BookStock::class);
    }

    /**
     * Get all of the deployments for the project.
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, BookStock::class);
    }
}
