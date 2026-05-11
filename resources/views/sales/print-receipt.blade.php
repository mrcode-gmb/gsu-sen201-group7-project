<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt #{{ $sale->unique_id }}</title>
    <style>
        @page {
            size: 80mm 297mm;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            width: 75mm;
            margin: 0 auto;
            padding: 2mm;
            line-height: 1.1;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-bold {
            font-weight: bold;
        }
        .text-large {
            font-size: 12px;
        }
        .text-xlarge {
            font-size: 14px;
        }
        .text-small {
            font-size: 9px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 3px 0;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 5px;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 5px;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        td {
            padding: 1px 0;
            max-width: 50%;
            word-wrap: break-word;
        }
        .item-name {
            width: 55%;
        }
        .item-price, .item-total {
            width: 22.5%;
            text-align: right;
        }
        .payment-method {
            text-transform: uppercase;
            font-weight: bold;
        }
        .business-info {
            margin-bottom: 3px;
            line-height: 1.2;
        }
    </style>
</head>
<body onload="window.print();">
    <div class="receipt-header">
        @if($sale->business)
            <div class="text-xlarge text-bold">{{ $sale->business->name }}</div>
            @if($sale->business->address)
                <div class="business-info text-small">{{ $sale->business->address }}</div>
            @endif
            @if($sale->business->phone)
                <div class="business-info">Tel: {{ $sale->business->phone }}</div>
            @endif
            @if($sale->business->email)
                <div class="business-info text-small">{{ $sale->business->email }}</div>
            @endif
            <div class="divider"></div>
        @else
            <div class="text-xlarge text-bold">{{ config('app.name') }}</div>
            <div>--------------------------</div>
        @endif
    </div>

    <div class="receipt-info">
        <div>#{{ $sale->unique_id }}</div>
        <div>Cashier: {{ substr($sale->user->name, 0, 15) }}</div>
        @if($sale->customer_name)
            <div>Receiver: {{ $sale->customer_name }}</div>
        @endif
        <div class="divider"></div>
    </div>

    <div class="receipt-items">
        <table>
            <tr>
                <td colspan="3" class="text-bold">{{ $sale->purchase->product->name }}</td>
            </tr>
            <tr>
                <td class="item-name">{{ number_format($sale->quantity, 1) }} x N{{ number_format($sale->selling_price_per_unit, 2) }}</td>
                <td class="item-total">N{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
        </table>
        <div class="divider"></div>
    </div>

    <div class="receipt-totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">N{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Payment:</td>
                <td class="text-right payment-method">{{ strtoupper(substr($sale->payment_type, 0, 8)) }}</td>
            </tr>
            <tr class="text-bold">
                <td>TOTAL:</td>
                <td class="text-right">N{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            @if($sale->payment_type === 'cash' && $sale->amount_tendered > 0)
            <tr>
                <td>Amount Tendered:</td>
                <td class="text-right">N{{ number_format($sale->amount_tendered, 2) }}</td>
            </tr>
            <tr>
                <td>Change:</td>
                <td class="text-right">N{{ number_format($sale->amount_tendered - $sale->total_amount, 2) }}</td>
            </tr>
            @endif
        </table>
        <div class="divider"></div>
    </div>

    <div class="receipt-footer">
        <div>Thank you for your business!</div>
        @if($sale->business)
            <div>{{ $sale->business->name }}</div>
        @else
            <div>{{ config('app.name') }}</div>
        @endif
        <div>{{ now()->format('M d, Y h:i A') }}</div>
    </div>

    <script>
        // Close the window after printing
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>
