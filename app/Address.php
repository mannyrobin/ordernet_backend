<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $primaryKey = 'ID';
    
    protected $table = "aradrs";

    protected $fillable = [
        'CUSTNO',
        'CSHIPNO',
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
        'GROUPID',
        'PASSWORD',
        'EMAIL',
        'PRICECODE',
        'PASSWORD2',
        'PASSWORD3',
        'PASSWORD4',
        'PASSWORD5',
        'PASSWORD6',
    ];
}
