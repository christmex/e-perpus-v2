<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'book_stock_id',
        'member_id',
        'transaction_book_qty',
        'transaction_loaned_at',
        'transaction_returned_at',
    ];

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            if($obj->id){
                Penalty::where('transaction_id', $obj->id)->where('penalty_status', '!=','unpaid')->delete();
            }
        });
    }

    public function member(){
        return $this->belongsTo(Member::class,'member_id','id');
    }
    public function penalty(){
        return $this->hasOne(Penalty::class,);
    }
    public function bookStock(){
        return $this->belongsTo(BookStock::class,'book_stock_id','id');
    }
    public function filterShowAll()
    {
        return '<a class="btn btn-sm btn-link" href="?filterShowAll=filterShowAll" data-toggle="tooltip" title="Filter"><i class="la la-filter"></i> Tampilkan Semua</a>';
    }
}
