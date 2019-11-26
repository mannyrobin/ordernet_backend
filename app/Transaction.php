<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $primaryKey = 'id';
    
    protected $table = 'artran';

    protected $fillable = [
        'custno',
        'invno',
        'invdte',
        'item',
        'descrip',
        'qtyshp',
        'price',
        'cost',
        'extprice',
        'disc',
        'taxrate',
        'ilot',
        'salesmn',
        'market',
        'arstat',
        'artype',
        'shipto',
    ];
}
