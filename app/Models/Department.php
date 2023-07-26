<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use CrudTrait;
    use HasFactory;
    protected $fillable = ['department_name'];

    public function setDepartmentNameAttribute($value)
    {
        $this->attributes['department_name'] = ucwords($value);
    }
}
