<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Address;
use App\Customer;
use App\Order;
use App\Transaction;

use DateTime;

class InvoiceController extends Controller
{
    public function index(Request $request) {
        $username = $request->username;
        $password = $request->password;
        $customerNumber = $request->customerNumber;

        $user = Address::where('CSHIPNO', $username)
            ->where('password', $password)->get();

        if ($user->count() != 0) {                                        
            //get invoice information
            $invoices__vCust = empty($customerNumber) ? "dfd" : $customerNumber;           
            $invoices = Order::where('custno', $invoices__vCust)
                ->orderBy('invno')
                ->get();

            $result_invoices = [];
            foreach($invoices as $invoice) {
                $result_invoices[] = [
                    'invoiceNumber' => $invoice->invno,
                    'invoiceDate' => $invoice->invdte,
                    'invoiceAmount' => $invoice->invamt,
                ];
            }

            return [
                'success' => '1',
                'invoices' => $result_invoices,
            ];
        }

        return [
            'success' => '0',
            'message' => 'login failed',
        ];

    }

    public function findone(Request $request) {
        $username = $request->username;
        $password = $request->password;
        $customerNumber = $request->customerNumber;
        $invoiceNumber = $request->invoiceNumber;

        $user = Address::where('CSHIPNO', $username)
            ->where('password', $password)->get();

        if ($user->count() != 0) {
            $invoiceNumber = str_repeat(" ", 8 - strlen($invoiceNumber)).$invoiceNumber;
            
            //get order information
            $order__MMColParam = empty($invoiceNumber) ? "1" : $invoiceNumber;
            $order__vCust = empty($customerNumber) ? "xyz" : $customerNumber;
            $orders = Order::where('invno', $invoiceNumber)
                ->where('custno', $order__vCust)
                ->get();

            //get detail information
            $detail__MMColParam = empty($invoiceNumber) ? "1" : $invoiceNumber;
            $detail__vCust = empty($customerNumber) ? "1" : $customerNumber;
            $details = Transaction::where('invno', $detail__MMColParam)
                ->where('custno', $detail__vCust)
                ->get();

            //get bill to information
            $billto__MMColParam = empty($customerNumber) ? "1" : $customerNumber;
            $billto = Address::where('CUSTNO', $billto__MMColParam)->get();

            //get tax sub information
            $taxSub__MMColParam = empty($invoiceNumber) ? "1" : $invoiceNumber;
            $taxSub__vCust = empty($customerNumber) ? "xyz" : $customerNumber;
            $taxSub = Transaction::selectRaw('subtotal = SUM(extprice), tax = SUM((extprice * taxrate) / 100)')
                ->where('invno', $taxSub__MMColParam)
                ->where('custno', $taxSub__vCust)
                ->where('taxrate', '>', '0')
                ->get();

            //get non tax sub information
            $nonTaxSub__MMColParam = empty($invoiceNumber) ? "1" : $invoiceNumber;
            $nonTaxSub__vCust = empty($customerNumber) ? "xyz" : $customerNumber;
            $nonTaxSub = Transaction::selectRaw('subtotal = SUM(extprice)')
                ->where('invno', $nonTaxSub__MMColParam)
                ->where('custno', $nonTaxSub__vCust)
                ->where('taxrate', '0')
                ->get();

            //get grand total information
            $grandTotal__MMColParam = empty($invoiceNumber) ? "1" : $invoiceNumber;
            $grandTotal__vCust = empty($customerNumber) ? "xyz" : $customerNumber;
            $grandTotal = Transaction::selectRaw('subtotal = SUM(extprice)')
                ->where('invno', $grandTotal__MMColParam)
                ->where('custno', $grandTotal__vCust)
                ->get();

            if ($orders->count() != 0) {
                $ship = $orders[0]->shipto;
                $shipto = [];

                $shipto__MMColParam = empty($customerNumber) ? "1" : $customerNumber;
                if (!empty($ship))
                    $shipto = Address::where('CUSTNO', $shipto__MMColParam)
                        ->where('CSHIPNO', $ship)
                        ->get();
                else
                    $shipto = Address::where('CUSTNO', $shipto__MMColParam)
                        ->get();
            }


            $result_selected_invoice = null;
            if ($orders->count() != 0) {
                $products = [];
                foreach($details as $detail) {
                    $product = [
                        'quantity' => $detail->qtyshp,
                        'item' => $detail->item,
                        'description' => $detail->descrip,
                        'price1' => $detail->price,
                        'extPrice' => $detail->extprice,
                    ];
                    $products[] = $product;
                }

                $result_selected_invoice = [
                    'billingCompany' => $billto[0]->COMPANY,
                    'billingAddress1' => $billto[0]->ADDRESS1,
                    'billingAddress2' => $billto[0]->ADDRESS2,
                    'billingCity' => $billto[0]->CITY,
                    'billingState' => $billto[0]->STATE, 
                    'billingZip' => $billto[0]->ZIP,

                    'shippingCompany' => $shipto[0]->COMPANY,
                    'shippingAddress1' => $shipto[0]->ADDRESS1,
                    'shippingAddress2' => $shipto[0]->ADDRESS2,
                    'shippingCity' => $shipto[0]->CITY,
                    'shippingState' => $shipto[0]->STATE,
                    'shippingZip' => $shipto[0]->ZIP,

                    'invoiceNumber' => $orders[0]->invno,
                    'invoiceDate' => $orders[0]->invdte,
                    'customerNumber' => $orders[0]->custno,
                    'fob' => $orders[0]->fob,
                    'shipVia' => $orders[0]->shipvia,
                    'slsm' => $orders[0]->salesmn,
                    'terms' => $orders[0]->pterms,
                    'poNumber' => $orders[0]->ponum,

                    'products' => $products,

                    "subtotal" => $nonTaxSub->count() === 0 ? 0 : $nonTaxSub[0]->subtotal,
                    "taxableSubtotal" => $taxSub->count() === 0 ? 0 : $taxSub[0]->subtotal,
                    "tax" => $taxSub->count() === 0 ? 0 : $taxSub[0]->tax,
                    "taxRate" => $orders[0]->taxrate,
                    "total" => $grandTotal->count() === 0 ? 0 : $grandTotal[0]->subtotal,
                ];
            }

            return [
                'success' => '1',
                'selectedinvoice' => $result_selected_invoice,
            ];
        }

        return [
            'success' => '0',
            'message' => 'login failed',
        ];

    }

    public function detail(Request $request) {
        $username = $request->username;
        $password = $request->password;
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;
        $customerNumber = $request->customerNumber;

        $user = Address::where('CSHIPNO', $username)
            ->where('password', $password)->get();

        if ($user->count() === 0)
            return [
                'success' => 0,
                'message' => 'login failed',
            ];
        
        $checkCust__MMColParam = empty($customerNumber) ? "1" : $customerNumber;
        $checkCust = Customer::where('CUSTNO', $checkCust__MMColParam)
            ->where('CORP', 'YES')
            ->get();

        $invoices__vFrom = new DateTime(empty($fromDate) ? "1/1/1970" : $fromDate);
        $invoices__vTo = new DateTime(empty($toDate) ? "1/1/1970" : $toDate);
        $invoices__vCust = empty($customerNumber) ? "abc" : $customerNumber;

        $invoices = Order::whereBetween('invdte', [$invoices__vFrom, $invoices__vTo])
            ->where('custno', $invoices__vCust)
            ->whereIn('artype', ['C', ' '])
            ->whereNotIn('artype', ['R', 'V'])
            ->get();

        $invoices__vFrom = new DateTime(empty($fromDate) ? "1/1/1970" : $fromDate);
        $invoices__vTo = new DateTime(empty($toDate) ? "1/1/1970" : $toDate);
        $totals__vCust = empty($customerNumber) ? "abc" : $customerNumber;

        $totals = Order::selectRaw('sum(invamt) as InvTotal, sum(tax) as TaxTotal')
            ->whereBetween('invdte', [$invoices__vFrom, $invoices__vTo])
            ->where('custno', $invoices__vCust)
            ->whereIn('artype', ['C', ' '])
            ->whereNotIn('artype', ['R', 'V'])
            ->get();

        $result_invoices = [];
        foreach($invoices as $invoice) {
            $details = Transaction::where('invno', $invoice->invno)
                ->where('custno', $invoice->custno)
                ->get();

            $invTotal = 0;            
            $products = [];

            foreach($details as $detail) {
                $product = [
                    'quantity' => $detail->qtyshp,
                    'item' => $detail->item,
                    'description' => $detail->descrip,
                    'price1' => $detail->price,
                    'extPrice' => $detail->extPrice,
                    'cost' => $detail->cost
                ];
                
                $invTotal += $detail->extprice;
                $products[] = $product;
            }

            $result_invoice = [
                'invoiceNumber' => $invoice->invno,
                'invoiceDate' => $invoice->invdte,
                'products' => $products,
                'invoiceAmount' => $invTotal,
            ];

            $result_invoices[] = $result_invoice;
        }

        return [
            'success' => 1,
            'invoices' => $result_invoices,
            'allInvoicesTotal' => $totals->count() === 0 ? 0 : $totals[0]->InvTotal ,
        ];
    }

    public function summary(Request $request) {
        $username = $request->username;
        $password = $request->password;
        $fromDate = $request->fromDate;
        $toDate = $request->toDate;
        $customerNumber = $request->customerNumber;

        $user = Address::where('CSHIPNO', $username)
            ->where('password', $password)->get();

        if ($user->count() === 0)
            return [
                'success' => 0,
                'message' => 'login failed',
            ];

        $checkCust__MMColParam = empty($customerNumber) ? "1" : $customerNumber;
        $checkCust = Customer::where('CUSTNO', $checkCust__MMColParam)
            ->where('CORP', 'YES')
            ->get();

        $invoices__vFrom = new DateTime(empty($fromDate) ? "1/1/1970" : $fromDate);
        $invoices__vTo = new DateTime(empty($toDate) ? "1/1/1970" : $toDate);
        $invoices__vCust = empty($customerNumber) ? "abc" : $customerNumber;

        $invoices = Order::whereBetween('invdte', [$invoices__vFrom, $invoices__vTo])
            ->where('custno', $invoices__vCust)
            ->whereIn('artype', ['C', ' '])
            ->whereNotIn('artype', ['R', 'V'])
            ->get();

        $invoices__vFrom = new DateTime(empty($fromDate) ? "1/1/1970" : $fromDate);
        $invoices__vTo = new DateTime(empty($toDate) ? "1/1/1970" : $toDate);
        $totals__vCust = empty($customerNumber) ? "abc" : $customerNumber;

        $totals = Order::selectRaw('sum(invamt) as InvTotal, sum(tax) as TaxTotal')
            ->whereBetween('invdte', [$invoices__vFrom, $invoices__vTo])
            ->where('custno', $invoices__vCust)
            ->whereIn('artype', ['C', ' '])
            ->whereNotIn('artype', ['R', 'V'])
            ->get();

        $result_invoices = [];
        foreach($invoices as $invoice) {
            $result_invoice = [
                'invoiceNumber' => $invoice['invno'],
                'orderNumber' => $invoice['ornum'],
                'invoiceDate' => $invoice['invdte'],
                'customerNumber' => $invoice['custno'],
                'shippingCompany' => $invoice['shipto'],
                'invoiceAmount' => $invoice['invamt'],
                'tax' => $invoice['tax'],
            ];

            $result_invoices[] = $result_invoice;
        }
        
        return [
            'success' => 1,
            'invoices' => $result_invoices,
            'allInvoicesTotal' => $totals->count() === 0 ? 0 : $totals[0]->InvTotal ,
            'allTaxTotal' => $totals->count() === 0 ? 0 : $totals[0]->TaxTotal ,
        ];
    }
}
