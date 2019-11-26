<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Address;
use App\Order;

use DateTime;

class StatementController extends Controller
{
    public function index(Request $request) {
        $username = $request->username;
        $password = $request->password;
        $customerNumber = $request->customerNumber;
        $ageFrom = $request->ageFrom;
        $period1 = $request->period1;
        $period2 = $request->period2;
        $period3 = $request->period3;
        $period4 = $request->period4;

        $user = Address::where('CSHIPNO', $username)
            ->where('password', $password)->get();

        if ($user->count() === 0)
            return [
                'success' => 0,
                'message' => 'login failed',
            ];

        $statments__vCust = empty($customerNumber) ? "dfd" : $customerNumber;
        $statements = Order::select(['invno', 'invdte', 'invamt', 'balance', 'pnet'])
            ->where('custno', $statments__vCust)
            ->where('arstat', '!=', 'V')
            ->where('balance', '!=', 0)
            ->groupBy(['invno', 'invdte', 'invamt', 'balance', 'pnet'])
            ->orderBy('invdte')
            ->get();

        $billto__MMColParam = empty($customerNumber) ? "1" : $customerNumber;
        $billto = Address::where('CUSTNO', $billto__MMColParam)
            ->get();

        $TotalBal__vCust = empty($customerNumber) ? "dfd" : $customerNumber;
        $TotalBal = Order::selectRaw('sum(balance) as Total')
            ->where('custno', $statments__vCust)
            ->where('arstat', '!=', 'V')
            ->where('balance', '!=', 0)
            ->groupBy('invdte')
            ->orderBy('invdte')
            ->get();
        
        $vBal1 = 0;
        $vBal2 = 0;
        $vBal3 = 0;
        $vBal4 = 0;
        $vBal5 = 0;
        $invoices = [];
        foreach($statements as $statement) {
            $curDate = new DateTime();
            $invDate = new DateTime($statement->invdte);

            $vDays = 0;
            if ($ageFrom === 'due') {
                $vDays = Abs(date_diff($curDate, $invDate)->format('%a')) + $statement->pnet;
                /*$invDate->modify("+".$statement->pnet." days");
                $v*Day = Abs(date_diff($curDate, $invDate))*/
            }
            else {
                $vDays = Abs(date_diff($curDate, $invDate)->format('%a'));
            }

            $invoice = [
                'invoiceNumber' => $statement->invno,
                'invoiceDate' => $statement->invdte,
                'invoiceAmount' => $statement->invamt,
                'invoiceBalance' => $statement->balance,
                'days' => $vDays,
                'start' => $period1
            ];
            
            $invoices[] = $invoice;

            if ($vDays < $period1) 
                $vBal1 += $statement->balance;
            else if ($period1 < $vDays && $vDays < $period2)
                $vBal2 += $statement->balance;
            else if ($period2 < $vDays && $vDays < $period3)
                $vBal3 += $statement->balance;
            else if ($period3 < $vDays && $vDays < $period4)
                $vBal4 += $statement->balance;
            else if ($period4 < $vDays)
                $vBal5 += $statement->balance;
        }

        $statement = [
            'billingCompany' => $billto[0]->COMPANY,
            'billingAddress1' => $billto[0]->ADDRESS1,
            'billingAddress2' => $billto[0]->ADDRESS2,
            'billingCity' => $billto[0]->CITY,
            'billingState' => $billto[0]->STATE, 
            'billingZip' => $billto[0]->ZIP,
            'billingEmail' => $billto[0]->EMAIL,
            'customerNumber' => $customerNumber,
            'statementDate' => date('n/d/Y'),

            'invoices' => $invoices,

            'currentDue' => $vBal1,
            'period1Due' => $vBal2,
            'period2Due' => $vBal3,
            'period3Due' => $vBal4,
            'period4Due' => $vBal5,
            'totalDue' => $TotalBal[0]->Total,
        ];

        return [
            'success' => '1',
            'statement' => $statement
        ];
    }
}
