<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Address;
use App\Systable;
use App\Invoice;

class CatalogController extends Controller
{
    public function index(Request $request) {
        $username = $request->username;
        $password = $request->password;

        $user = Address::where('CSHIPNO', $username)
            ->where('password', $password)->get();

        if ($user->count() != 0) {
            $types = Systable::select(['TBLDESC', 'TBLCHAR'])
                ->leftJoin('arinvt', 'arinvt.CODE', '=', 'systable.TBLCHAR')
                ->groupBy(['TBLDESC', 'TBLCHAR'])
                ->orderBy('TBLDESC')
                ->get();
                        
            $catalogs = [];

            foreach($types as $type) {
                $catalog = Invoice::select(['DESCRIP', 'CODE', 'UNITMS', 'TBLDESC'])
                    ->leftJoin('systable', 'arinvt.CODE', '=', 'systable.TBLCHAR')
                    ->where('CODE', $type->TBLCHAR)
                    ->orderBy('CODE')
                    ->get();
                
                $catalogs[] = $catalog;
            }

            return [
                'success' => '1',
                'catalogs' => $catalogs,
            ];
        }

        return [
            'success' => '0',
            'message' => 'login failed',
        ];
    }
}
