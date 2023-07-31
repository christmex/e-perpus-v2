<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLocation extends Model
{
    use CrudTrait;
    use HasFactory;
    
    protected $fillable = ['book_location_name','book_location_label'];

    public function setBookLocationNameAttribute($value)
    {
        $this->attributes['book_location_name'] = ucwords($value);
    }
    public function setBookLocationLabelAttribute($value)
    {
        $this->attributes['book_location_label'] = ucwords($value);
    }
    public function bookStocks(){
        return $this->hasMany(BookStock::class);
    }
}
