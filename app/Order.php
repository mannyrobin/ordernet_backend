<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'ID';
    
    protected $table = 'armast';

    protected $fillable = [
        'custno',
        'invno',
        'invdte',
        'invamt',
        'paidamt',
        'balance',
        'dtepaid',
        'salesmn',
        'ponum',
        'refno',
        'taxrate',
        'tax',
        'arstat',
        'artype',
        'shipto',
        'shipvia',
        'pterms',
        'ordate',
        'pnet',
        'fob',
        'terr',
        'disc',
        'disamt',
        'pdisc',
        'pdays',
        'ornum',
    ];
}
