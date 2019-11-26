<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $primaryKey = 'companyid';
    
    protected $table = 'Companies';

    protected $fillable = [
        'companyid',
        'company',
        'menuTag',
        'mainText',
        'promotions',
        'cretid',
        'Commodities',
    ];
}
