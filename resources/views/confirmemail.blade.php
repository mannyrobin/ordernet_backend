<html>
    <body>
        <font color="black" face="Courier New, Courier, mono" size="2"-->
            <strong>
                <i>This is to confirm that we have received your order:</i>
            </strong>
            <br/>
            <br/>
            <strong>
                Order Number: {{ $order->ORDERID }} <br/>
                &nbsp;&nbsp;&nbsp;&nbsp;Ship Date: {{ date('m/d/Y', strtotime($order->SHIPDATE)) }}
            </strong>
            <br/><br/>
            Items Ordered:
            <br/><br/>
            <table table style='border:1px solid black;' width='100%' cellpadding='3' cellspacing='3'>
                <tr style='background-color:#000080;color:#ffffff'>
                    <td>Item#</td>
                    <td>Description</td>
                    <td>Qty</td>
                    <td>Price</td>
                    <td>Extprice</td>
                </tr>
                @php
                    $cellColor = "#E8E8E8";
                @endphp
                @foreach ($detail as $item)
                    <tr style="background-color: {{$cellColor}};">
                        <td>{{ $item->ITEM }}</td>
                        <td>{{ $item->DESCRIP }}</td>
                        <td>{{ $item->QTYORD }}</td>
                        <td>{{ $item->PRICE }}</td>
                        <td>{{ $item->PRICE * $item->QTYORD }}</td>
                    </tr>
                    @php
                        if ($cellColor === "#E8E8E8")
                            $cellColor = "#FFFFFF";
                        else
                            $cellColor = "#E8E8E8";
                    @endphp
                @endforeach
                <tr style="background-color: #000080;color: #FFFFFF;">
                    <td>Total</td>
                    <td></td>
                    <td>{{ $totalQty->total }}</td>
                    <td></td>
                    <td>{{ $order->total }}</td>
                </tr>
            </table>
            <br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Prices Subject to Change<br/>
            <strong>Ship To:</strong>
            <br/>{{ $order->SHIPTO }}
            <br/>{{ $order->COMPANY }}
            <br/>{{ $order->CONTACT }}
            <br/>{{ $order->ADDRESS1 }}
            <br/>{{ $order->ADDRESS2 }}
            <br/>{{ $order->CITY }}
            <br/>{{ $order->STATE }}
            <br/>{{ $order->ZIP }}
            <br/>{{ $order->COUNTRY }}
            <br/>

            <strong>Comment:</strong>
            <br/>{{ $order->COMMENT }}
            <br/>

            <strong>We thank you for your order.<br/><br/></strong>
        </font>
    </body>
</html>



