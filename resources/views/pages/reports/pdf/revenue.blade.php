<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue Report</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h2 { margin: 0 0 5px 0; color: #1e3a8a; }
        .header p { margin: 0; color: #666; font-size: 11px; }
        .summary { margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; background: #f9fafb; display: table; width: 100%; box-sizing: border-box; }
        .summary-col { display: table-cell; width: 50%; }
        .summary-col strong { font-size: 15px; color: #1e3a8a; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; color: #475569; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f8fafc; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Revenue Report</h2>
        <p>Period: {{ $date_from ? \Carbon\Carbon::parse($date_from)->format('d M Y') : 'Start' }} to {{ $date_to ? \Carbon\Carbon::parse($date_to)->format('d M Y') : 'Today' }}</p>
        <p>Generated on: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-col">
            <span>Project Count:</span> <br>
            <strong>{{ $projectCount }}</strong>
        </div>
        <div class="summary-col text-right">
            <span>Total Revenue:</span> <br>
            <strong>₹ {{ number_format($totalRevenue, 2) }}</strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Project Number</th>
                <th>Customer Name</th>
                <th>Quotation Number</th>
                <th>Completion Date</th>
                <th>Created By</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $row)
                <tr>
                    <td><strong>{{ $row['project_number'] }}</strong></td>
                    <td>{{ $row['customer_name'] }}</td>
                    <td>{{ $row['quotation_number'] }}</td>
                    <td>{{ $row['completion_date'] }}</td>
                    <td>{{ $row['created_by'] }}</td>
                    <td class="text-right">₹ {{ number_format($row['revenue_amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right">Total Revenue:</td>
                <td class="text-right">₹ {{ number_format($totalRevenue, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
