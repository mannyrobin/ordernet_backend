<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Systable extends Model
{
    protected $primaryKey = 'TBLCHAR';

    protected $table = 'systable';

    public $incrementing = false;

    protected $fillable = [
        'TBLCHAR',
        'TBLID',
        'TBLDESC',
    ];
}
