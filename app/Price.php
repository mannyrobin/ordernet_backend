<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $primaryKey = 'ID';
    
    protected $table = 'arpric';

    protected $fillable = [
        'CUSTNO',
        'ITEM',
        'PRICE',
        'citem',
    ];
}
