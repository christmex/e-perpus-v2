<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use CrudTrait;
    use HasFactory;

    public $loanExpDays = 3;

    protected $fillable = [
        'book_stock_id',
        'member_id',
        'transaction_book_qty',
        'transaction_loaned_at',
        'transaction_returned_at',
    ];

    public function member(){
        return $this->belongsTo(Member::class,'member_id','id');
    }
    // public function bookStock(){
    //     return $this->belongsTo(BookStock::class,'book_stock_id','id');
    // }
}
