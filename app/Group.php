<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $primaryKey = 'ID';
    
    protected $table = 'argprc';

    protected $fillable = [
        'GROUPID',
        'ITEM',
        'PRICE',
        'disc',
    ];
}
