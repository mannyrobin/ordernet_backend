<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Address;
use App\Systable;

class CustomerController extends Controller
{
    //
    public function getuserinfo(Request $request) {
        $username = $request->username;
        $password = $request->password;

        $user = Address::where('CSHIPNO', $username)
            ->where('password', $password)->get();

        if ($user->count() === 0) 
            return [
                'success' => '0',
                'message' => 'login failed',
            ];
        
        $userinfo = Address::selectRaw('aradrs.ID as id, aradrs.CUSTNO as custno, aradrs.CSHIPNO as cshipno, aradrs.COMPANY as company,
            aradrs.CONTACT as contact, aradrs.TITLE as title, aradrs.ADDRESS1 as address1, aradrs.ADDRESS2 as address2,
            aradrs.CITY as city, aradrs.STATE as state, aradrs.ZIP as zip, aradrs.COUNTRY as country, aradrs.PHONE as phone,
            aradrs.GROUPID as groupid, aradrs.EMAIL as email, aradrs.PRICECODE as pricecode,
            arcust.COMPANY as billingcompany, arcust.CONTACT as billingcontact, arcust.TITLE as billingtitle,
            arcust.ADDRESS1 as billingaddress1, arcust.ADDRESS2 as billingaddress2,
            arcust.CITY as billingcity, arcust.STATE as billingstate, arcust.ZIP as billingzip,
            arcust.COUNTRY as billingcountry, arcust.PHONE as billingphone')
        ->join('arcust', 'arcust.CUSTNO', '=', 'aradrs.CUSTNO')
        ->where('aradrs.CSHIPNO', $username)
        ->where('aradrs.password', $password)
        ->get()[0];

        return [
            'success' => '1',
            'userinfo' => $userinfo
        ];
    }

    public function customer(Request $request) {
        $username = $request->username;
        $password = $request->password;

        $user = Address::where('CSHIPNO', $username)
            ->where('password', $password)->get();

        if ($user->count() != 0) {
            $userinfo = Address::selectRaw('aradrs.ID as id, aradrs.CUSTNO as custno, aradrs.CSHIPNO as cshipno, aradrs.COMPANY as company,
                    aradrs.CONTACT as contact, aradrs.TITLE as title, aradrs.ADDRESS1 as address1, aradrs.ADDRESS2 as address2,
                    aradrs.CITY as city, aradrs.STATE as state, aradrs.ZIP as zip, aradrs.COUNTRY as country, aradrs.PHONE as phone,
                    aradrs.GROUPID as groupid, aradrs.EMAIL as email, aradrs.PRICECODE as pricecode,
                    arcust.COMPANY as billingcompany, arcust.CONTACT as billingcontact, arcust.TITLE as billingtitle,
                    arcust.ADDRESS1 as billingaddress1, arcust.ADDRESS2 as billingaddress2,
                    arcust.CITY as billingcity, arcust.STATE as billingstate, arcust.ZIP as billingzip,
                    arcust.COUNTRY as billingcountry, arcust.PHONE as billingphone')
                ->join('arcust', 'arcust.CUSTNO', '=', 'aradrs.CUSTNO')
                ->where('aradrs.CSHIPNO', $username)
                ->where('aradrs.password', $password)
                ->get()[0];
            
            $products = DB::select('exec MoreProducts "'.$userinfo->cshipno.'", "'.$userinfo->pricecode.'"');

            $productcodes = Systable::select('TBLDESC', 'TBLCHAR')->get();

            return [
                'success' => 1,
                'userinfo' => $userinfo,
                'productcodes' => $productcodes,
                'products' => $products,
            ];
        }

        return [
            'success' => '0',
            'message' => 'login failed',
        ];
    }
}
