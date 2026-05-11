<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payment Receipt #{{ $transaction->id }}</title>
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
        }
    </style>
</head>
<body onload="window.print();">
    <div class="receipt-header">
        <div class="text-xlarge text-bold">{{ $transaction->creditor->business->name }}</div>
        <div class="text-small">{{ $transaction->creditor->business->address }}</div>
        <div>Tel: {{ $transaction->creditor->business->phone }}</div>
        <div class="divider"></div>
        <div class="text-large text-bold">PAYMENT RECEIPT</div>
    </div>

    <div class="receipt-info">
        <div>Receipt #: {{ $transaction->id }}</div>
        <div>Date: {{ $transaction->created_at->format('M d, Y h:i A') }}</div>
        <div>Creditor: {{ $transaction->creditor->name }}</div>
        <div class="divider"></div>
    </div>

    <div class="receipt-items">
        <table>
            <tr>
                <td>Description:</td>
                <td class="text-right">{{ $transaction->description }}</td>
            </tr>
        </table>
        <div class="divider"></div>
    </div>

    <div class="receipt-totals">
        <table>
            <tr class="text-bold">
                <td>Amount Paid:</td>
                <td class="text-right">N{{ number_format($transaction->amount, 2) }}</td>
            </tr>
            <tr>
                <td>New Balance:</td>
                <td class="text-right">N{{ number_format($transaction->running_balance, 2) }}</td>
            </tr>
        </table>
        <div class="divider"></div>
    </div>

    <div class="receipt-footer">
        <div>Thank you for your payment!</div>
        <div>{{ now()->format('M d, Y h:i A') }}</div>
    </div>

    <script>
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>
