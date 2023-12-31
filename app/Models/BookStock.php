<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookStock extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'book_id',
        'book_location_id',
        'book_stock_qty',
        'book_description',
    ];

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            if($obj->id){
                Transaction::where('book_stock_id', $obj->id)->where('transaction_returned_at', '!=',NULL)->delete();
            }
        });
    }

    public function getBookLocationNameAttribute()
    {
        return $this->bookLocation->book_location_name;
    }
    public function getBookNameAttribute()
    {
        return $this->book->book_name;
    }

    public function book(){
        return $this->belongsTo(Book::class,'book_id','id');
    }
    public function bookLocation(){
        return $this->belongsTo(BookLocation::class,'book_location_id','id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

}
