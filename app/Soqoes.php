<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Soqoes extends Model
{
    protected $primaryKey = 'ID';
    
    protected $table = 'soqoes';

    public $timestamps = false;

    protected $fillable = [
        'ITEM',
        'ID',
        'ORDERID',
        'CUSTNO',
        'SHORTDESC',
        'QTYORD',
        'ORDATE',
        'RQDATE',
        'ENTERDATE',
        'SHIPTO',
        'QOSTAT',
        'UNITMS',
        'ROUTE',
        'STSEQ',
        'HOLD',
        'PRICE',
        'QONUM',
        'PONUM',
        'DECNUM',
        'PIC',
        'ASTERISK',
        'BACKORD',
        'UMQTY',
        'TERR',
        'WEIGHT',
        'UNITPR',
        'WLIST',
        'PACK',
        'SIZE',
        'UNITCOST',
        'RETAIL',
        'UNITRETAIL',
        'UPCCODE'
    ];
}
