<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'CUSTNO';
    
    protected $table = 'arcust';

    protected $fillable = [
        'PASSWORD',
        'LOC',
        'COMPANY',
        'CONTACT',
        'TITLE',
        'ADDRESS1',
        'ADDRESS2',
        'CITY',
        'STATE',
        'ZIP',
        'COUNTRY',
        'PHONE',
        'BALANCE',
        'PRICECODE',
        'FAXNO',
        'EMAIL',
        'GROUPID',
        'TYPE',
        'CORP',
    ];
}
