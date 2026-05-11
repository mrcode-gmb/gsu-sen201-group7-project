<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Assignments Report - {{ now()->format('Y-m-d') }}</title>
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
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-in_progress {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-overdue {
            background-color: #fee2e2;
            color: #991b1b;
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
        <h1>Product Assignments Report</h1>
        <p>Generated on: {{ now()->format('F j, Y h:i A') }}</p>
    </div>

    @if(isset($filters) && (isset($filters['start_date']) || isset($filters['end_date']) || isset($filters['status'])))
    <div class="filters">
        <h3>Filters Applied:</h3>
        @if(isset($filters['start_date']))
            <p><strong>From:</strong> {{ \Carbon\Carbon::parse($filters['start_date'])->format('M d, Y') }}</p>
        @endif
        @if(isset($filters['end_date']))
            <p><strong>To:</strong> {{ \Carbon\Carbon::parse($filters['end_date'])->format('M d, Y') }}</p>
        @endif
        @if(isset($filters['status']) && $filters['status'] !== 'all')
            <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $filters['status'])) }}</p>
        @endif
        @if(isset($filters['user_id']))
            <p><strong>Staff:</strong> {{ $filters['user_name'] }}</p>
        @endif
    </div>
    @endif

    @if($assignments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Staff</th>
                    <th>Product</th>
                    <th>Assigned Qty</th>
                    <th>Sold Qty</th>
                    <th>Remaining</th>
                    <th>Price/Unit</th>
                    <th>Status</th>
                    <th>Assigned Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $index => $assignment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $assignment->user->name }}</td>
                        <td>{{ $assignment->purchase->product->name ?? 'N/A' }}</td>
                        <td>{{ $assignment->assigned_quantity }}</td>
                        <td>{{ $assignment->sold_quantity ?? 0 }}</td>
                        <td>{{ $assignment->assigned_quantity - ($assignment->sold_quantity ?? 0) }}</td>
                        <td>₦{{ number_format($assignment->expected_selling_price, 2) }}</td>
                        <td>
                            @php
                                $statusClass = 'status-' . $assignment->status;
                                if ($assignment->isOverdue() && $assignment->status !== 'completed') {
                                    $statusClass = 'status-overdue';
                                    $status = 'Overdue';
                                } else {
                                    $status = str_replace('_', ' ', $assignment->status);
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td>{{ $assignment->assigned_date->format('M d, Y') }}</td>
                        <td>{{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right font-bold">Total Assigned:</td>
                    <td class="font-bold">{{ $assignments->sum('assigned_quantity') }}</td>
                    <td class="font-bold">{{ $assignments->sum('sold_quantity') }}</td>
                    <td class="font-bold">{{ $assignments->sum('assigned_quantity') - $assignments->sum('sold_quantity') }}</td>
                    <td colspan="4"></td>
                </tr>
            </tbody>
        </table>
    @else
        <p>No assignments found for the selected filters.</p>
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
