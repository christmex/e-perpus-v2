<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    use CrudTrait;
    use HasFactory;
    
    protected $fillable = [
        'transaction_id',
        // 'penalty_status_id',
        'penalty_status',
        'penalty_cost',
    ];

    
    public function getBookNameAttribute()
    {
        return $this->transaction->bookStock->book->book_name;
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }
}
