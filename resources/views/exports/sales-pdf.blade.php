<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Goods Dispatch Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 5px 0 0 0;
            padding: 0;
        }
        .filters {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .filters p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            margin-top: 0;
            font-size: 16px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        .summary-item {
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .summary-item .title {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .summary-item .amount {
            font-size: 16px;
            font-weight: bold;
            color: #212529;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Goods Dispatch Report</h1>
        <p>Generated on: {{ now()->format('F j, Y h:i A') }}</p>
    </div>

    @if(isset($filters) && (isset($filters['start_date']) || isset($filters['end_date']) || isset($filters['payment_type'])))
    <div class="filters">
        <h3>Filters Applied:</h3>
        @if(isset($filters['start_date']))
            <p><strong>From:</strong> {{ \Carbon\Carbon::parse($filters['start_date'])->format('M d, Y') }}</p>
        @endif
        @if(isset($filters['end_date']))
            <p><strong>To:</strong> {{ \Carbon\Carbon::parse($filters['end_date'])->format('M d, Y') }}</p>
        @endif
        @if(isset($filters['payment_type']) && $filters['payment_type'] !== 'all')
            <p><strong>Payment Method:</strong> {{ $paymentTypes[$filters['payment_type']] ?? ucfirst($filters['payment_type']) }}</p>
        @endif
    </div>
    @endif

    @if($sales->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    @if(isset($filters['include_customer']) && $filters['include_customer'])
                        <th>Receiver</th>
                    @endif
                    @if(isset($filters['include_salesperson']) && $filters['include_salesperson'])
                        <th>Staff</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                        <td>
                            {{ $sale->purchase->product->name }}
                            @if($sale->purchase->product->brand)
                                ({{ $sale->purchase->product->brand }})
                            @endif
                        </td>
                        <td>{{ $sale->quantity }} {{ $sale->purchase->product->unit_type }}</td>
                        <td>₦{{ number_format($sale->selling_price_per_unit, 2) }}</td>
                        <td>₦{{ number_format($sale->total_amount, 2) }}</td>
                        <td>{{ $paymentTypes[$sale->payment_type] ?? ucfirst($sale->payment_type) }}</td>
                        @if(isset($filters['include_customer']) && $filters['include_customer'])
                            <td>{{ $sale->customer_name ?? 'N/A' }}</td>
                        @endif
                        @if(isset($filters['include_salesperson']) && $filters['include_salesperson'])
                            <td>{{ $sale->user->name }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>Dispatch Summary</h3>
            <div class="summary-grid">
                @foreach($paymentSummary as $payment)
                    <div class="summary-item">
                        <div class="title">{{ $paymentTypes[$payment->payment_type] ?? ucfirst($payment->payment_type) }}</div>
                        <div class="amount">₦{{ number_format($payment->total, 2) }}</div>
                        <div class="count">{{ $payment->count }} {{ Str::plural('dispatch', $payment->count) }}</div>
                    </div>
                @endforeach
                
                <div class="summary-item" style="background-color: #e7f5ff;">
                    <div class="title">Total Dispatch Value</div>
                    <div class="amount">₦{{ number_format($totalSales, 2) }}</div>
                    <div class="count">{{ $sales->total() }} {{ Str::plural('dispatch', $sales->total()) }}</div>
                </div>
                
                @if(isset($totalProfit))
                <div class="summary-item" style="background-color: #ebfbee;">
                    <div class="title">Estimated Margin</div>
                    <div class="amount">₦{{ number_format($totalProfit, 2) }}</div>
                </div>
                @endif
            </div>
        </div>
    @else
        <p>No sales data available for the selected filters.</p>
    @endif

    <div class="footer">
        <p>Generated by {{ config('app.name') }} • Page <span class="pageNumber"></span> of <span class="totalPages"></span></p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
