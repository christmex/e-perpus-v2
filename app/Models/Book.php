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

    // public static function boot()
    // {
    //     parent::boot();
    //     static::deleting(function($obj) {
    //         if($obj->book_cover){
    //             Storage::disk('public')->delete($obj->book_cover);
    //         }
    //     });
    // }

    public function setBookNameAttribute($value)
    {
        $this->attributes['book_name'] = ucwords($value);
    }

    public function getAllBookStockAttribute()
    {
        $stock = 0;
        foreach ($this->bookStock as $value) {
            $stock += $value->book_stock_qty;
        }
        foreach ($this->transactions as $value) {
            // if($value->transaction_returned_at == null){
            //     $stock += $value->transaction_book_qty;
            // }
            $stock += $value->transaction_book_qty;
        }
        return $stock;
        
    }

    public function bookStock(){
        return $this->hasMany(BookStock::class);
    }

    public function allTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, BookStock::class);
    }

    public function transactions()
    {
        return $this->allTransactions()->where('transaction_returned_at','=',null);
    }
}
