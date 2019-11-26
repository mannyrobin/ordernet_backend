<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Soqoem extends Model
{
    protected $primaryKey = 'ORDERID';
    
    protected $table = 'soqoem';

    public $timestamps = false;

    protected $fillable = [
        'CUSTNO',
        'ORDERID',
        'ORDATE',
        'RQDATE',
        'ENTERDATE',
        'SHIPDATE',
        'SHIPTO',
        'SIGNATURE',
        'QONUM',
        'PONUM',
        'VASSEL',
        'RYANNO',
        'DELDATE',
        'BOOK',
        'SEAL',
        'CONTAIN',
        'TERR',
        'COMMENT',
        'CONTACT',
        'ADDRESS1',
        'ADDRESS2',
        'CITY',
        'STATE',
        'ZIP',
        'COUNTRY',
        'TOTAL',
        'ORDERID2'
    ];
}