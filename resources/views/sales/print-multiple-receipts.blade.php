<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sales Receipt</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.35;
            width: 72mm;
            margin: 0 auto;
            padding: 2mm;
            -webkit-print-color-adjust: exact;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }


        .text-large {
            font-size: 12px;
        }

        .text-xlarge {
            font-size: 14px;
        }

        .text-small {
            font-size: 11px;
        }

        .text-bold {
            font-weight: 700;
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

        .item-price,
        .item-total {
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

        .my-10 {
            margin: 1rem, 0;
        }

        .my-5 {
            margin-bottom: 10px;
        }
        .my-halp {
            margin: 10px 0;
        }

        .mb-5 {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="receipt-header my-5">
        @if ($business)
            <div class="text-xlarge text-bold my-5">{{ $business->name }}</div>
            @if ($business->address)
                <div class="business-info text-small">{{ $business->address }}</div>
            @endif
            @if ($business->phone)
                <div class="business-info">Tel: {{ $business->phone }}</div>
            @endif
            @if ($business->email)
                <div class="business-info text-small">{{ $business->email }}</div>
            @endif
            <div class="divider"></div>
        @else
            <div class="text-xlarge text-bold">{{ config('app.name') }}</div>
            <div>--------------------------</div>
        @endif
    </div>

    @foreach ($sales as $sale)
        {{-- <div class="receipt-info my-10">
            <div>#{{ $sale->id }} | {{ $sale->created_at->format('M d, Y h:i A') }}</div>
            <div>Cashier: {{ substr($sale->user->name, 0, 15) }}</div>
            @if ($sale->customer_name)
                <div>Receiver: {{ $sale->customer_name }}</div>
            @endif
            <div class="divider"></div>
        </div> --}}

        <div class="receipt-items my-halp">
            <table>
                <tr>
                    <td colspan="3" class="text-bold">{{ $sale->purchase->product->name }}</td>
                </tr>
                <tr>
                    <td class="item-name"> 
                        <p>Quantity: {{ number_format($sale->quantity, 1) }}</p>
                        <p>Paid Amount: <del>N</del>{{ number_format($sale->total_amount, 2) }}</p>
                    </td>
                </tr>
            </table>
        </div>


    @endforeach

    <br><br>
    <table>
        <tr class="text-bold">
            <td colspan="3" style="text-align: right;">Total Paid:</td>
            <td><del>N</del>{{ number_format($sales->sum('total_amount'), 2) }}</td>
        </tr>
    </table>

    <div class="receipt-footer mb-5">
        <div>Thank you for your business!</div>
        <div>{{ now()->format('M d, Y h:i A') }}</div>
    </div>
</body>

</html>
