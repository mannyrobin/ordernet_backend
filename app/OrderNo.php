<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderNo extends Model
{
    protected $primaryKey = 'ID';
    
    protected $table = 'ordno';

    protected $fillable = [];
}
