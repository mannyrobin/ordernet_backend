<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $primaryKey = 'ID';
    
    protected $table = 'arinvt';

    protected $fillable = [
        'ITEM',
        'DESCRIP',
        'PRICE',
        'PRICE2',
        'PRICE3',
        'PRICE4',
        'PRICE5',
        'PRICE6',
        'PRICE7',
        'PRICE8',
        'PRICE9',
        'UNITMS',
        'UMQTY',
        'UMDESC',
        'WEIGHT',
        'CODE',
        'upccode',
        'PRICE10',
        'PRICE11',
        'PRICE12',
        'PRICE13',
        'PRICE14',
        'PRICE15',
        'PRICE16',
        'PRICE17',
        'PRICE18',
        'PRICE19',
        'PRICE20',
        'PRICE1',
        'COMPNO',
    ];
}
