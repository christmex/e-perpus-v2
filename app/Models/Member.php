<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Member extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'member_name',
        'department_id',
        'member_phone_number',
        'member_profile_picture',
    ];

    // public static function boot()
    // {
    //     parent::boot();
    //     static::deleting(function($obj) {
    //         if($obj->member_profile_picture){
    //             Storage::disk('public')->delete($obj->member_profile_picture);
    //         }
    //     });
    // }
    public function setMemberNameAttribute($value)
    {
        $this->attributes['member_name'] = ucwords($value);
    }

    public function department(){
        return $this->belongsTo(Department::class,'department_id','id');
    }
}
