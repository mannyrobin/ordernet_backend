<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Address;
use App\Soqoem;
use App\Soqoes;

use DateTime;

class OrderController extends Controller
{
    public function submit(Request $request) {
        $username = $request->username;
        $password = $request->password;
        $productsInfo = $request->products;
        $shipDate = $request->shipdate;
        $custComments = $request->comments;
        $custPONum = $request->ponum;

        $user = Address::where('CSHIPNO', $username)
            ->where('password', $password)->get();

        if ($user->count() === 0)
            return [
                'success' => 0,
                'message' => 'login failed',
            ];

        

        $products = [];
        $orderTotal = 0;

        foreach ($productsInfo as $productInfo) {           
            $product = [
                'quantity' => $productInfo['quantity'],
                'item' => $productInfo['item'],
                'price1' => $productInfo['price1'],
                'upccode' => $productInfo['upccode'],
                'descrip' => $productInfo['descrip'],
            ];
            $products[] = $product;
            $orderTotal += $productInfo['quantity'] * $productInfo['price1'];
        }

        $custNo = $user[0]->CUSTNO;
        $shipTo = $user[0]->CSHIPNO;
        $custPhone = $user[0]->PHONE;
        $custContact = $user[0]->CONTACT;
        $custAddress1 = $user[0]->ADDRESS1;
        $custAddress2 = $user[0]->ADDRESS2;
        $custCity = $user[0]->CITY;
        $custState = $user[0]->STATE;
        $custZip = $user[0]->ZIP;
        $custCountry = $user[0]->COUNTRY;
        $orderID2 = $custNo.date('ndYhisA');

        DB::beginTransaction();

        $soqoem = new Soqoem();

        $soqoem->ORDERID2 = $orderID2;
        $soqoem->CUSTNO = $custNo;
        $soqoem->SHIPTO = $shipTo;
        $soqoem->ORDATE = new DateTime();
        $soqoem->TOTAL = $orderTotal;
        $soqoem->SHIPDATE = $shipDate;
        $soqoem->PONUM = $custPONum;
        $soqoem->COMMENT = $custComments;
        $soqoem->CONTACT = $custContact;
        $soqoem->ADDRESS1 = $custAddress1;
        $soqoem->ADDRESS2 = $custAddress2;
        $soqoem->CITY = $custCity;
        $soqoem->STATE = $custState;
        $soqoem->ZIP = $custZip;
        $soqoem->COUNTRY = $custCountry;
        $soqoem->save();

        $realSoqoem = Soqoem::where('ORDERID2', $orderID2)->get();
        $orderId = $realSoqoem[0]->ORDERID;

        foreach($products as $product) {
            $soqoes = new Soqoes();
            $soqoes->ORDERID = $orderId;
            $soqoes->QTYORD = $product['quantity'];
            $soqoes->ITEM = $product['item'];
            $soqoes->PRICE = $product['price1'];
            $soqoes->UPCCODE = $product['upccode'];
            $soqoes->SHORTDESC = $product['descrip'];
            $soqoes->CUSTNO = $custNo;
            $soqoes->save();
        }

        DB::commit();

        return [
            'success' => 1,
            'orderID' => $orderId
        ];
    }
}
