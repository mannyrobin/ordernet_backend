<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Address;
use App\Customer;
use App\Soqoem;
use App\Soqoes;
use App\Mail\SendMail;

use DateTime;

class OrderController extends Controller
{
    public function submit(Request $request) {
        $username = $request->username;
        $password = $request->password;
        $productsInfo = $request->products;
        $shipDate = $request->shipDate;
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

        // send email with $username and $orderId
        $order__MMColParam = empty($orderId) ? "1" : $orderId;
        $order = Soqoem::selectRaw('t1.ORDERID, t1.ORDATE, t1.SHIPTO, t1.PONUM, t1.TOTAL, t1.COMMENT, t1.SHIPDATE, t1.CONTACT, t1.ADDRESS1, t1.ADDRESS2, t1.CITY, t1.STATE, t1.ZIP, t1.COUNTRY, t2.COMPANY, t2.email')
            ->fromRaw('dbo.soqoem t1, dbo.aradrs t2')
            ->where('t1.ORDERID', '=', $order__MMColParam)
            ->whereRaw('t1.CUSTNO = t2.CUSTNO')
            ->get();

        $totalQty__MMColParam = empty($orderId) ? "1" : $orderId;
        $totalQty = Soqoes::selectRaw('sum(QTYORD) as total')
            ->where('ORDERID', '=', $orderId)
            ->get();

        if ($totalQty->count() === 0) 
            $totalQty = [
                'total' => 0
            ];
        else
            $totalQty = $totalQty[0];

        $detail__MMColParam = empty($orderId) ? "1" : $orderId;
        $detail = Soqoes::selectRaw('t1.ITEM, t1.PRICE, t1.QTYORD, t2.DESCRIP')
            ->fromRaw('dbo.soqoes t1, dbo.arinvt t2')
            ->where('t1.ORDERID', '=', $orderId)
            ->whereRaw('t1.ITEM = t2.ITEM')
            ->get();

        $loginInfo__MMColParam = empty($username) ? "1" : $username;
        $loginInfo = Customer::where("CUSTNO", '=', $loginInfo__MMColParam)
            ->get();

        $to_name = $loginInfo[0]->COMPANY;
        $to_email = $loginInfo[0]->EMAIL;
        $data = [
            'order'     => $order[0],
            'totalQty'  => $totalQty,
            'detail'    => $detail
        ];

        Mail::send('confirmemail', $data, function($message) use ($to_name, $to_email, $order) {
            $message->to(explode(';', $to_email), $to_name)->subject('Order Confirmation from Lucky Produce ('.$order[0]->ORDERID.')')
                ->cc(['akaraksya@dproduceman.com', 'sales@dproduceman.com']);
            $message->from('sales@dproduceman.com', 'Lucky Produce');
        });

        DB::commit();

        return [
            'success' => 1,
            'orderID' => $orderId
        ];
    }
}
